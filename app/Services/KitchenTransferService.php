<?php

namespace App\Services;

use App\Exceptions\KitchenStockException;
use App\Models\KitchenLocation;
use App\Models\KitchenStock;
use App\Models\KitchenTransferRequest;
use App\Models\StockTransaction;
use App\Models\StoreNotification;
use App\Models\StoreStock;
use App\Models\StoreUser;
use Illuminate\Support\Facades\DB;

/**
 * GM -> Area Manager kitchen transfers (kitchen spec 7.1, contract B.5).
 * - kitchen_transfer_request: create requests for your own store.
 * - kitchen_transfer_approve: approve / deny requests of stores you manage
 *   (your store, or the stores you can switch to: group / Super Admin).
 * - The requester can never decide their own request (checked here, on the
 *   server, not just hidden in the page).
 * - Nothing moves until approval; approval moves shelf -> kitchen in one
 *   locked transaction and writes both ledgers.
 */
class KitchenTransferService
{
    public function __construct(private KitchenInventoryService $inventory)
    {
    }

    public static function canRequest(StoreUser $user): bool
    {
        return $user->hasPermission('kitchen_transfer_request');
    }

    /** Store ids whose requests this user may decide. */
    public static function approvableStoreIds(StoreUser $user): array
    {
        if (!$user->hasPermission('kitchen_transfer_approve')) {
            return [];
        }

        return collect([$user->store_id])->merge($user->switchableStores()->pluck('id'))->filter()->unique()->values()->all();
    }

    public static function canDecide(StoreUser $user, KitchenTransferRequest $req): bool
    {
        return $req->status === KitchenTransferRequest::PENDING
            && (int) $req->requested_by !== (int) $user->id
            && in_array((int) $req->store_id, array_map('intval', self::approvableStoreIds($user)), true);
    }

    /** @param array<int, array{product_id:int, quantity:float}> $lines */
    public function create(StoreUser $user, array $lines, ?string $notes): KitchenTransferRequest
    {
        if (!self::canRequest($user)) {
            throw new KitchenStockException('You are not allowed to request kitchen transfers.');
        }

        $lines = collect($lines)->filter(fn ($l) => !empty($l['product_id']) && (float) ($l['quantity'] ?? 0) > 0)
            ->groupBy('product_id')
            ->map(fn ($g, $pid) => ['product_id' => (int) $pid, 'quantity' => round($g->sum(fn ($l) => (float) $l['quantity']), 2)])
            ->values();
        if ($lines->isEmpty()) {
            throw new KitchenStockException('Add at least one item with a quantity.');
        }

        // The product must be on this store's shelf (checked again on approval).
        $onShelf = StoreStock::where('store_id', $user->store_id)->whereIn('product_id', $lines->pluck('product_id'))->pluck('quantity', 'product_id');
        foreach ($lines as $l) {
            if (!$onShelf->has($l['product_id'])) {
                throw new KitchenStockException('One of the items is not stocked at this store.');
            }
        }

        $req = DB::transaction(function () use ($user, $lines, $notes) {
            $req = KitchenTransferRequest::create([
                'store_id' => $user->store_id,
                'kitchen_location_id' => $this->kitchenFor($user->store_id)->id,
                'status' => KitchenTransferRequest::PENDING,
                'notes' => $notes,
                'requested_by' => $user->id,
                'requested_by_name' => $user->name,
            ]);
            foreach ($lines as $l) {
                $req->items()->create($l);
            }

            return $req;
        });

        $this->notifyApprovers($req);

        return $req;
    }

    public function approve(StoreUser $user, KitchenTransferRequest $req, ?string $note = null): void
    {
        $this->assertCanDecide($user, $req);

        DB::transaction(function () use ($user, $req, $note) {
            $req = KitchenTransferRequest::whereKey($req->id)->lockForUpdate()->firstOrFail();
            if ($req->status !== KitchenTransferRequest::PENDING) {
                throw new KitchenStockException('This request was already ' . strtolower($req->statusLabel()) . '.');
            }

            foreach ($req->items()->with('product')->get() as $line) {
                $shelf = StoreStock::where('store_id', $req->store_id)->where('product_id', $line->product_id)->lockForUpdate()->first();
                $qty = (float) $line->quantity;
                if (!$shelf || (float) $shelf->quantity < $qty) {
                    throw new KitchenStockException('Not enough ' . ($line->product->product_name ?? 'stock') . ' on the store shelf ('
                        . rtrim(rtrim(number_format((float) ($shelf->quantity ?? 0), 2), '0'), '.') . ' left). Deny the request or wait for stock.');
                }

                $shelf->quantity = round((float) $shelf->quantity - $qty, 2);
                $shelf->save();

                $kitchenStock = KitchenStock::firstOrCreate(
                    ['kitchen_location_id' => $req->kitchen_location_id, 'item_type' => 'product', 'item_id' => $line->product_id],
                    ['quantity' => 0, 'unit' => $line->product->unit ?? null]
                );
                $this->inventory->move($kitchenStock, 'transfer_in', $qty, null, "Request {$req->number()} by {$req->requested_by_name}, approved by {$user->name}");

                StockTransaction::create([
                    'store_id' => $req->store_id,
                    'product_id' => $line->product_id,
                    'type' => 'kitchen_transfer_out',
                    'quantity_change' => -$qty,
                    'running_balance' => $shelf->quantity,
                    'ware_user_id' => $user->id,
                    'remarks' => "Kitchen transfer {$req->number()} approved by {$user->name}",
                ]);
            }

            $req->update([
                'status' => KitchenTransferRequest::APPROVED,
                'decided_by' => $user->id,
                'decided_by_name' => $user->name,
                'decided_at' => now(),
                'decision_note' => $note,
            ]);
        });

        $this->notifyRequester($req->fresh(), 'approved');
    }

    public function deny(StoreUser $user, KitchenTransferRequest $req, string $reason): void
    {
        $this->assertCanDecide($user, $req);

        $updated = KitchenTransferRequest::whereKey($req->id)->where('status', KitchenTransferRequest::PENDING)->update([
            'status' => KitchenTransferRequest::DENIED,
            'decided_by' => $user->id,
            'decided_by_name' => $user->name,
            'decided_at' => now(),
            'decision_note' => $reason,
        ]);
        if (!$updated) {
            throw new KitchenStockException('This request was already decided.');
        }

        $this->notifyRequester($req->fresh(), 'denied');
    }

    /** The requester may withdraw a request while it is still pending. */
    public function cancel(StoreUser $user, KitchenTransferRequest $req): void
    {
        if ((int) $req->requested_by !== (int) $user->id || $req->status !== KitchenTransferRequest::PENDING) {
            throw new KitchenStockException('Only the person who made a pending request can cancel it.');
        }
        $req->update(['status' => KitchenTransferRequest::CANCELLED, 'decided_at' => now(), 'decided_by_name' => $user->name, 'decision_note' => 'Cancelled by requester']);
    }

    protected function assertCanDecide(StoreUser $user, KitchenTransferRequest $req): void
    {
        if ((int) $req->requested_by === (int) $user->id) {
            throw new KitchenStockException('You cannot approve or deny your own request. An Area Manager has to decide it.');
        }
        if (!self::canDecide($user, $req)) {
            throw new KitchenStockException($req->status !== KitchenTransferRequest::PENDING
                ? 'This request was already ' . strtolower($req->statusLabel()) . '.'
                : 'You are not allowed to decide kitchen transfers for this store.');
        }
    }

    protected function kitchenFor(int $storeId): KitchenLocation
    {
        return KitchenLocation::firstOrCreate(['store_id' => $storeId, 'type' => 'store'], ['name' => 'Store Kitchen']);
    }

    /** In-app notice to everyone who can decide this store's requests (not the requester). */
    protected function notifyApprovers(KitchenTransferRequest $req): void
    {
        StoreUser::where('is_active', true)->where('id', '!=', $req->requested_by)->get()
            ->filter(fn (StoreUser $u) => in_array((int) $req->store_id, array_map('intval', self::approvableStoreIds($u)), true))
            ->each(fn (StoreUser $u) => StoreNotification::create([
                'user_id' => $u->id,
                'store_id' => $u->store_id,
                'title' => 'Kitchen transfer needs approval',
                'message' => "{$req->requested_by_name} requested {$req->items()->count()} item(s) for the " . ($req->store->store_name ?? 'store') . " kitchen ({$req->number()}).",
                'type' => 'warning',
                'url' => route('kitchen-transfers.show', $req->id),
            ]));
    }

    protected function notifyRequester(KitchenTransferRequest $req, string $what): void
    {
        if (!$req->requested_by) {
            return;
        }
        StoreNotification::create([
            'user_id' => $req->requested_by,
            'store_id' => $req->store_id,
            'title' => 'Kitchen transfer ' . $what,
            'message' => "{$req->number()} was {$what} by {$req->decided_by_name}" . ($req->decision_note ? ": {$req->decision_note}" : '.'),
            'type' => $what === 'approved' ? 'success' : 'danger',
            'url' => route('kitchen-transfers.show', $req->id),
        ]);
    }
}

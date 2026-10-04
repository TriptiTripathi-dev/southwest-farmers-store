<?php

namespace App\Services;

use App\Exceptions\KitchenStockException;
use App\Models\KitchenStock;
use App\Models\KitchenStockTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Every change to kitchen stock goes through here (kitchen spec section 6):
 * the balance and its ledger row are written together in one locked
 * transaction, so the current quantity is always explained by the ledger.
 */
class KitchenInventoryService
{
    public const WASTE_REASONS = ['Spoiled', 'Expired', 'Damaged', 'Dropped / Spilled', 'Other'];
    public const ADJUST_REASONS = ['Count correction', 'Found stock', 'Returned to store', 'Data entry error', 'Other'];

    /**
     * Move kitchen stock by a signed amount. Out-movements may not take the
     * balance below zero.
     */
    public function move(KitchenStock $stock, string $type, float $change, ?string $reason = null, ?string $notes = null): KitchenStockTransaction
    {
        if (!array_key_exists($type, KitchenStockTransaction::TYPES)) {
            throw new \InvalidArgumentException("Unknown kitchen stock movement [{$type}]");
        }

        return DB::transaction(function () use ($stock, $type, $change, $reason, $notes) {
            $locked = KitchenStock::whereKey($stock->id)->lockForUpdate()->firstOrFail();
            $this->recordOpeningBalance($locked);

            $before = (float) $locked->quantity;
            $after = round($before + $change, 2);
            if ($after < 0) {
                throw new KitchenStockException('Not enough in the kitchen: ' . $this->qty($before) . ' on hand, tried to take ' . $this->qty(abs($change)) . '.');
            }

            $locked->quantity = $after;
            $locked->save();
            $stock->setRawAttributes($locked->getAttributes(), true);

            return $this->ledger($locked, $type, $change, $reason, $notes);
        });
    }

    /**
     * Physical count: the counted quantity becomes the balance through a
     * 'count' ledger row (never a silent overwrite).
     */
    public function count(KitchenStock $stock, float $counted, ?string $notes = null): ?KitchenStockTransaction
    {
        if ($counted < 0) {
            throw new KitchenStockException('A counted quantity cannot be negative.');
        }

        return DB::transaction(function () use ($stock, $counted, $notes) {
            $locked = KitchenStock::whereKey($stock->id)->lockForUpdate()->firstOrFail();
            $diff = round($counted - (float) $locked->quantity, 2);
            if ($diff == 0.0) {
                return null; // count matches: nothing to record
            }

            return $this->move($locked, 'count', $diff, 'Count correction', $notes);
        });
    }

    /**
     * Stock that reached the kitchen before the ledger existed gets one
     * "Opening balance" row so its history adds up.
     */
    protected function recordOpeningBalance(KitchenStock $stock): void
    {
        if ((float) $stock->quantity != 0.0 && !$stock->transactions()->exists()) {
            $this->ledger($stock, 'opening', (float) $stock->quantity, null, 'Balance before the kitchen ledger started');
        }
    }

    protected function ledger(KitchenStock $stock, string $type, float $change, ?string $reason, ?string $notes): KitchenStockTransaction
    {
        $user = Auth::user();

        return KitchenStockTransaction::create([
            'kitchen_location_id' => $stock->kitchen_location_id,
            'kitchen_stock_id' => $stock->id,
            'type' => $type,
            'quantity_change' => round($change, 2),
            'balance_after' => $type === 'opening' ? round($change, 2) : (float) $stock->quantity,
            'reason' => $reason,
            'notes' => $notes,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
        ]);
    }

    protected function qty(float $q): string
    {
        return rtrim(rtrim(number_format($q, 2), '0'), '.');
    }
}

<?php

namespace App\Http\Controllers\Store;

use App\Exceptions\KitchenStockException;
use App\Http\Controllers\Controller;
use App\Models\KitchenStock;
use App\Models\KitchenTransferRequest;
use App\Models\StoreDetail;
use App\Models\StoreStock;
use App\Services\KitchenTransferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Kitchen transfers (kitchen spec 7.1 request/approval, 7.3 report).
 * The list doubles as the transfer report: one row per item, with
 * requester, approver, dates, status and current kitchen quantity; CSV export.
 */
class KitchenTransferController extends Controller
{
    public function __construct(private KitchenTransferService $transfers)
    {
    }

    /** Stores whose transfers this user may see: own store + stores they approve for. */
    protected function visibleStoreIds(): array
    {
        $user = Auth::user();

        return collect([$user->store_id])->merge(KitchenTransferService::approvableStoreIds($user))->unique()->values()->all();
    }

    protected function authorizeView(): void
    {
        $u = Auth::user();
        abort_unless($u->hasPermission('kitchen_transfer_request') || $u->hasPermission('kitchen_transfer_approve') || $u->hasPermission('adjust_stock'), 403);
    }

    protected function findVisible(int $id): KitchenTransferRequest
    {
        return KitchenTransferRequest::whereIn('store_id', $this->visibleStoreIds())->with('items.product', 'store')->findOrFail($id);
    }

    public function index(Request $request)
    {
        $this->authorizeView();
        $user = Auth::user();
        $tz = config('app.display_timezone', 'America/Chicago');
        $storeIds = $this->visibleStoreIds();

        $query = KitchenTransferRequest::whereIn('store_id', $storeIds)->with('items.product', 'store')->latest('created_at')->latest('id');
        if ($request->filled('store_id') && in_array((int) $request->store_id, array_map('intval', $storeIds), true)) {
            $query->where('store_id', $request->integer('store_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', Carbon::parse($request->from, $tz)->startOfDay()->utc());
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', Carbon::parse($request->to, $tz)->endOfDay()->utc());
        }
        if ($request->filled('item')) {
            $term = '%' . $request->item . '%';
            $query->whereHas('items.product', fn ($q) => $q->where('product_name', 'ilike', $term));
        }

        // Current kitchen quantity per (kitchen, product), for the report column.
        $current = fn ($req, $productId) => KitchenStock::where('kitchen_location_id', $req->kitchen_location_id)->where('item_type', 'product')->where('item_id', $productId)->value('quantity');

        if ($request->input('export') === 'csv') {
            return $this->csv($query->get(), $current, $tz);
        }

        $requests = $query->paginate(20)->withQueryString();
        $stores = StoreDetail::whereIn('id', $storeIds)->orderBy('store_name')->get(['id', 'store_name']);
        $awaitingMe = KitchenTransferRequest::whereIn('store_id', KitchenTransferService::approvableStoreIds($user))
            ->where('status', KitchenTransferRequest::PENDING)->where('requested_by', '!=', $user->id)->count();
        $canRequest = KitchenTransferService::canRequest($user);
        $statuses = KitchenTransferRequest::STATUS_LABELS;

        return view('store.kitchen-transfers.index', compact('requests', 'stores', 'awaitingMe', 'canRequest', 'statuses', 'current', 'tz'));
    }

    public function create()
    {
        abort_unless(KitchenTransferService::canRequest(Auth::user()), 403, 'You are not allowed to request kitchen transfers.');

        $storeStocks = StoreStock::where('store_id', Auth::user()->store_id)->where('quantity', '>', 0)->with('product')->get()
            ->sortBy(fn ($s) => $s->product->product_name ?? '')->values();

        return view('store.kitchen-transfers.create', compact('storeStocks'));
    }

    public function store(Request $request)
    {
        abort_unless(KitchenTransferService::canRequest(Auth::user()), 403);
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $req = $this->transfers->create(Auth::user(), $data['items'], $data['notes'] ?? null);
        } catch (KitchenStockException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('kitchen-transfers.show', $req->id)
            ->with('success', "Request {$req->number()} sent. It is pending Area Manager approval; kitchen stock changes only after approval.");
    }

    public function show(int $id)
    {
        $this->authorizeView();
        $req = $this->findVisible($id);
        $user = Auth::user();
        $canDecide = KitchenTransferService::canDecide($user, $req);
        $isOwn = (int) $req->requested_by === (int) $user->id;
        $tz = config('app.display_timezone', 'America/Chicago');
        $shelf = StoreStock::where('store_id', $req->store_id)->whereIn('product_id', $req->items->pluck('product_id'))->pluck('quantity', 'product_id');
        $kitchen = KitchenStock::where('kitchen_location_id', $req->kitchen_location_id)->where('item_type', 'product')
            ->whereIn('item_id', $req->items->pluck('product_id'))->pluck('quantity', 'item_id');

        return view('store.kitchen-transfers.show', compact('req', 'canDecide', 'isOwn', 'tz', 'shelf', 'kitchen'));
    }

    public function approve(Request $request, int $id)
    {
        $req = $this->findVisible($id);
        $request->validate(['note' => 'nullable|string|max:500']);

        try {
            $this->transfers->approve(Auth::user(), $req, $request->note);
        } catch (KitchenStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "{$req->number()} approved. The stock moved from the store shelf to the kitchen.");
    }

    public function deny(Request $request, int $id)
    {
        $req = $this->findVisible($id);
        $data = $request->validate(['reason' => 'required|string|max:500'], ['reason.required' => 'Give a reason for denying.']);

        try {
            $this->transfers->deny(Auth::user(), $req, $data['reason']);
        } catch (KitchenStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "{$req->number()} denied. No stock moved.");
    }

    public function cancel(int $id)
    {
        $req = $this->findVisible($id);

        try {
            $this->transfers->cancel(Auth::user(), $req);
        } catch (KitchenStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "{$req->number()} cancelled.");
    }

    /** Transfer report as CSV (opens in Excel): one row per requested item. */
    protected function csv($requests, $current, string $tz)
    {
        $fmt = fn ($d) => $d ? $d->copy()->timezone($tz)->format('m/d/Y h:i A') : '';
        $rows = [['Request #', 'Store', 'Requested at', 'Requested by', 'Item', 'Quantity', 'Status', 'Approved/denied by', 'Decided at', 'Decision note', 'Request notes', 'Current kitchen qty']];
        foreach ($requests as $r) {
            foreach ($r->items as $line) {
                $rows[] = [
                    $r->number(), $r->store->store_name ?? '', $fmt($r->created_at), $r->requested_by_name,
                    $line->product->product_name ?? '', (float) $line->quantity, $r->statusLabel(),
                    $r->decided_by_name, $fmt($r->decided_at), $r->decision_note, $r->notes,
                    (float) ($current($r, $line->product_id) ?? 0),
                ];
            }
        }

        $name = 'kitchen-transfers-' . now($tz)->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv']);
    }
}

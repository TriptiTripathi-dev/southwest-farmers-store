<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CateringUrgencyTier;
use App\Models\Sale;
use App\Models\StoreSetting;
use App\Services\CateringService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Store side of catering (kitchen spec 8): the catering orders list and the
 * store's catering rules (standard notice, cutoff time, urgency tiers).
 * Rule changes need 'update_settings'; the real tiers/percentages are the
 * client's decision, so they are data, not code.
 */
class StoreCateringController extends Controller
{
    protected function authorizeRules(): void
    {
        abort_unless(Auth::user()?->hasPermission('update_settings'), 403, 'You do not have permission to change catering rules.');
    }

    public function index(Request $request)
    {
        $storeId = Auth::user()->store_id;
        $tz = CateringService::tz();
        $view = $request->input('view', 'upcoming'); // upcoming | past | all

        $query = Sale::with('items.menuItem', 'customer')->where('store_id', $storeId)->where('order_type', 'Catering');
        $startOfToday = now($tz)->startOfDay()->utc();
        if ($view === 'upcoming') {
            $query->where('due_at', '>=', $startOfToday)->orderBy('due_at');
        } elseif ($view === 'past') {
            $query->where('due_at', '<', $startOfToday)->orderByDesc('due_at');
        } else {
            $query->orderByDesc('due_at');
        }
        if ($request->filled('date')) {
            $day = Carbon::parse($request->date, $tz);
            $query->whereBetween('due_at', [$day->copy()->startOfDay()->utc(), $day->copy()->endOfDay()->utc()]);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('store.kitchen.catering.index', compact('orders', 'view', 'tz'));
    }

    public function rules(CateringService $catering)
    {
        $storeId = Auth::user()->store_id;
        $options = StoreSetting::cateringOptions($storeId);
        $tiers = $catering->tiers($storeId);
        $canEdit = Auth::user()->hasPermission('update_settings');

        return view('store.kitchen.catering.rules', compact('options', 'tiers', 'canEdit'));
    }

    public function updateRules(Request $request)
    {
        $this->authorizeRules();
        $data = $request->validate([
            'catering_notice_days' => 'required|integer|min:0|max:60',
            'catering_cutoff_time' => 'required|date_format:H:i',
        ]);

        StoreSetting::updateOrCreate(['store_id' => Auth::user()->store_id], $data + ['catering_enabled' => $request->boolean('catering_enabled')]);

        return back()->with('success', 'Catering rules saved.');
    }

    public function storeTier(Request $request)
    {
        $this->authorizeRules();
        $storeId = Auth::user()->store_id;
        $notice = StoreSetting::cateringOptions($storeId)['notice_days'];

        $data = $request->validate([
            'min_days' => 'required|integer|min:0|max:60',
            'max_days' => 'required|integer|min:0|max:60|gte:min_days',
            'action' => 'required|in:surcharge,block',
            'percent' => 'required_if:action,surcharge|nullable|numeric|min:0|max:200',
        ], ['max_days.gte' => '"To" days must be the same or more than "From" days.']);

        if ($data['max_days'] >= $notice) {
            return back()->withInput()->with('error', "Tiers are for orders inside the standard notice, so \"To\" must be less than {$notice} days.");
        }
        $overlap = CateringUrgencyTier::where('store_id', $storeId)
            ->where('min_days', '<=', $data['max_days'])->where('max_days', '>=', $data['min_days'])->exists();
        if ($overlap) {
            return back()->withInput()->with('error', 'That range overlaps an existing tier. Delete or change the other tier first.');
        }

        CateringUrgencyTier::create($data + ['store_id' => $storeId, 'percent' => $data['action'] === 'block' ? 0 : (float) $data['percent']]);

        return back()->with('success', 'Urgency tier added.');
    }

    public function destroyTier(int $tier)
    {
        $this->authorizeRules();
        CateringUrgencyTier::where('store_id', Auth::user()->store_id)->findOrFail($tier)->delete();

        return back()->with('success', 'Urgency tier removed.');
    }

    /** One click: the illustrative tiers from the spec, for the client to edit. */
    public function loadExamples()
    {
        $this->authorizeRules();
        $storeId = Auth::user()->store_id;
        if (CateringUrgencyTier::where('store_id', $storeId)->exists()) {
            return back()->with('error', 'Remove the existing tiers first.');
        }
        $notice = StoreSetting::cateringOptions($storeId)['notice_days'];
        $examples = collect([
            ['min_days' => 4, 'max_days' => 6, 'action' => 'surcharge', 'percent' => 10],
            ['min_days' => 2, 'max_days' => 3, 'action' => 'surcharge', 'percent' => 20],
            ['min_days' => 0, 'max_days' => 1, 'action' => 'block', 'percent' => 0],
        ])->filter(fn ($t) => $t['max_days'] < $notice);
        foreach ($examples as $t) {
            CateringUrgencyTier::create($t + ['store_id' => $storeId]);
        }

        return back()->with('success', 'Example tiers added (4–6 days +10%, 2–3 days +20%, under 2 days blocked). Change them to the real values.');
    }
}

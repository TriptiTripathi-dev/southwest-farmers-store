<?php

namespace App\Services;

use App\Models\CateringUrgencyTier;
use App\Models\MenuItem;
use App\Models\StoreSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Catering pricing rules (kitchen spec 8.1, contract B.7). One place decides
 * notice, surcharge or block, so the website preview and the order the
 * server saves always match.
 *
 * Notice = whole calendar days from the order day to the requested day, in
 * store local time. An order placed after the store's daily cutoff counts
 * from the next day. At or above the standard notice (default 7): normal
 * price. Inside it: the matching urgency tier applies (surcharge % or block);
 * no matching tier = not accepted.
 */
class CateringService
{
    public static function tz(): string
    {
        return config('app.display_timezone', 'America/Chicago');
    }

    /** @return Collection<int, CateringUrgencyTier> */
    public function tiers(int $storeId): Collection
    {
        return CateringUrgencyTier::where('store_id', $storeId)->orderBy('min_days')->get();
    }

    public function noticeDays(int $storeId, Carbon $dueAt, ?Carbon $now = null): int
    {
        $tz = self::tz();
        $now = ($now ?? now())->copy()->timezone($tz);
        $orderDay = $now->copy()->startOfDay();
        if ($now->format('H:i') >= StoreSetting::cateringOptions($storeId)['cutoff_time']) {
            $orderDay->addDay();
        }

        return (int) $orderDay->diffInDays($dueAt->copy()->timezone($tz)->startOfDay(), false);
    }

    /**
     * @param array<int|string, float|int|string> $quantities menu_item_id => qty
     * @return array{ok:bool, message:?string, notice_days:int, standard_days:int, tier:?string, percent:float, lines:array, subtotal:float, surcharge:float, total:float}
     */
    public function quote(int $storeId, array $quantities, Carbon $dueAt, ?Carbon $now = null): array
    {
        $opt = StoreSetting::cateringOptions($storeId);
        $items = MenuItem::where('store_id', $storeId)->where('is_active', true)
            ->whereIn('id', array_keys($quantities))->get()->keyBy('id');

        $lines = [];
        foreach ($quantities as $id => $qty) {
            $qty = (float) $qty;
            if ($qty <= 0 || !$items->has((int) $id)) {
                continue;
            }
            $item = $items[(int) $id];
            $lines[] = ['menu_item_id' => $item->id, 'name' => $item->name, 'quantity' => $qty, 'price' => (float) $item->price, 'total' => round($qty * (float) $item->price, 2)];
        }
        $subtotal = round(array_sum(array_column($lines, 'total')), 2);
        $notice = $this->noticeDays($storeId, $dueAt, $now);

        $result = ['ok' => true, 'message' => null, 'notice_days' => $notice, 'standard_days' => $opt['notice_days'],
            'tier' => null, 'percent' => 0.0, 'lines' => $lines, 'subtotal' => $subtotal, 'surcharge' => 0.0, 'total' => $subtotal];

        $fail = fn (string $m) => ['ok' => false, 'message' => $m] + $result;

        if (!$opt['enabled']) {
            return $fail('Catering orders are not being accepted at this store right now.');
        }
        if (!$lines) {
            return $fail('Add at least one item.');
        }
        if ($dueAt->copy()->timezone(self::tz())->lessThanOrEqualTo(($now ?? now())->copy()->timezone(self::tz()))) {
            return $fail('Choose a date and time in the future.');
        }
        if ($notice >= $opt['notice_days']) {
            return $result; // standard pricing
        }

        $tier = $this->tiers($storeId)->first(fn ($t) => $notice >= $t->min_days && $notice <= $t->max_days);
        if (!$tier || $tier->action === 'block') {
            return $fail("Catering needs at least {$opt['notice_days']} days' notice"
                . ". Orders with {$notice} day" . ($notice === 1 ? '' : 's') . "' notice can't be accepted. Please choose a later date.");
        }

        $surcharge = round($subtotal * $tier->percent / 100, 2);

        return ['tier' => $tier->label(), 'percent' => (float) $tier->percent, 'surcharge' => $surcharge, 'total' => round($subtotal + $surcharge, 2)] + $result;
    }
}

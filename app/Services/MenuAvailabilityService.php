<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\MenuItemAvailabilityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Daily availability changes (kitchen spec 4.3): store-specific, and every
 * change is logged with who and when. is_available_today is kept in step for
 * older screens.
 */
class MenuAvailabilityService
{
    public function set(MenuItem $item, string $status): bool
    {
        if (!in_array($status, [MenuItem::AVAILABLE, MenuItem::UNAVAILABLE, MenuItem::SOLD_OUT], true)) {
            throw new \InvalidArgumentException("Unknown availability status [{$status}]");
        }

        $from = $item->availability_status ?: MenuItem::AVAILABLE;
        $isSameDaySoldOut = $status === MenuItem::SOLD_OUT && $from === MenuItem::SOLD_OUT
            && $item->sold_out_on?->toDateString() === MenuItem::localToday()->toDateString();
        if ($from === $status && ($status !== MenuItem::SOLD_OUT || $isSameDaySoldOut)) {
            return false; // nothing changes
        }

        $user = Auth::user();

        DB::transaction(function () use ($item, $status, $from, $user) {
            $item->forceFill([
                'availability_status' => $status,
                'sold_out_on' => $status === MenuItem::SOLD_OUT ? MenuItem::localToday()->toDateString() : null,
                'is_available_today' => $status === MenuItem::AVAILABLE,
                'availability_changed_at' => now(),
                'availability_changed_by' => $user?->name,
            ])->save();

            MenuItemAvailabilityLog::create([
                'menu_item_id' => $item->id,
                'store_id' => $item->store_id,
                'from_status' => $from,
                'to_status' => $status,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);
        });

        return true;
    }
}

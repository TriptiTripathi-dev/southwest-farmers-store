<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;

/**
 * Old kitchen "Staff & Timesheets" (separate kitchen shifts and clock).
 * Kitchen spec 9-10 asks for one schedule and time clock for all staff, so
 * kitchen shifts now live in Schedules and clock-ins in the Staff Time Clock
 * (the migration copied the old kitchen data across). These routes stay so
 * existing links keep working.
 */
class StoreKitchenStaffScheduleController extends Controller
{
    protected function moved()
    {
        return redirect()->route('store.schedules.index')
            ->with('success', 'Kitchen shifts and timesheets are now part of Schedules and the Staff Time Clock (one system for all staff). Use the Role / station field for kitchen stations.');
    }

    public function index()
    {
        return $this->moved();
    }

    public function storeShift()
    {
        return $this->moved();
    }

    public function updateShift()
    {
        return $this->moved();
    }

    public function destroyShift()
    {
        return $this->moved();
    }

    public function clockIn()
    {
        return redirect()->route('store.time-clock.index');
    }

    public function clockOut()
    {
        return redirect()->route('store.time-clock.index');
    }
}

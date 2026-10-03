<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\QuickPosSetting;
use App\Models\StoreDetail;
use App\Services\PosAgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickPosSettingController extends Controller
{
    public function edit(PosAgentService $posAgentService)
    {
        $settings = QuickPosSetting::first() ?: QuickPosSetting::create([]);
        $store = StoreDetail::where('id', Auth::user()->store_id)->first();

        $isTerminalConnected = false;
        $hardware = null;
        if ($store && $posAgentService->isConfigured()) {
            $hardware = $posAgentService->getHardwareStatus($store->pos_terminal_id);
            $isTerminalConnected = $hardware['online'];

            // Sync status to DB
            $currentApiStatus = $isTerminalConnected ? 'online' : 'offline';
            if ($store->pos_terminal_status !== $currentApiStatus) {
                $store->update(['pos_terminal_status' => $currentApiStatus]);
            }
        }

        return view('settings.quick_pos_page', compact('settings', 'store', 'isTerminalConnected', 'hardware'));
    }

    public function update(Request $request)
    {
        $settings = QuickPosSetting::first() ?: QuickPosSetting::create([]);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'pos_terminal_id' => 'nullable|string|max:255',
            'pos_agent_secret' => 'nullable|string|max:255',
            'pos_store_id' => 'nullable|string|max:255',
            'pos_hardware_url' => 'nullable|url|max:255',
        ]);

        // Hardware toggles checkboxes from form (if not checked, they are absent from request)
        $settingsData = [
            'title' => $data['title'] ?? $settings->title,
            'subtitle' => $data['subtitle'] ?? $settings->subtitle,
            'printer_enabled' => $request->has('printer_enabled'),
            'scanner_enabled' => $request->has('scanner_enabled'),
            'scale_enabled' => $request->has('scale_enabled'),
            'cash_drawer_enabled' => $request->has('cash_drawer_enabled'),
            'pax_enabled' => $request->has('pax_enabled'),
            'auto_print_receipt' => $request->has('auto_print_receipt'),
        ];

        $settings->update($settingsData);

        // Update POS credentials if provided in the exact same form
        $storeUpdates = [];
        if ($request->has('pos_terminal_id')) $storeUpdates['pos_terminal_id'] = $request->pos_terminal_id;
        if ($request->has('pos_agent_secret')) $storeUpdates['pos_agent_secret'] = $request->pos_agent_secret;
        if ($request->has('pos_store_id')) $storeUpdates['pos_store_id'] = $request->pos_store_id;
        if ($request->has('pos_hardware_url')) $storeUpdates['pos_hardware_url'] = $request->pos_hardware_url;

        if (!empty($storeUpdates)) {
            StoreDetail::where('id', Auth::user()->store_id)->update($storeUpdates);
        }

        return back()->with('success', 'Quick POS settings updated successfully.');
    }

    /**
     * "Check Connection": the hardware service has no register endpoint
     * (spec 25 Sep 2026); the register is found by POS Store ID + Agent
     * Secret, so this checks the scanner and scale through the service.
     */
    public function connectToServer(Request $request, PosAgentService $posAgentService)
    {
        $store = StoreDetail::where('id', Auth::user()->store_id)->firstOrFail();

        if (!$posAgentService->isConfigured()) {
            return back()->with('error', $posAgentService->notConfiguredMessage() . ' Save them first, then check the connection.');
        }

        $hw = $posAgentService->getHardwareStatus($store->pos_terminal_id);
        $store->update(['pos_terminal_status' => $hw['online'] ? 'online' : 'offline']);

        if (!$hw['online']) {
            return back()->with('error', 'Register not reachable: ' . ($hw['message'] ?? 'hardware agent offline.'));
        }

        return back()->with('success', 'Register connected. Scanner: ' . ($hw['scanner'] ? 'OK' : 'not connected')
            . ', Scale: ' . ($hw['scale'] ? 'OK' : 'not connected') . '.');
    }

    public function testDrawer(PosAgentService $posAgentService)
    {
        $store = StoreDetail::where('id', Auth::user()->store_id)->first();

        $response = $posAgentService->openCashDrawer($store->pos_terminal_id ?? null);
        return response()->json($response);
    }
}

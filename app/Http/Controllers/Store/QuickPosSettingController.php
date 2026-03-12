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
        if ($store && $store->pos_terminal_id) {
            $statusResponse = $posAgentService->getTerminalStatus($store->pos_terminal_id);

            // The API returns 'success' and 'registered' boolean flags
            $isTerminalConnected = isset($statusResponse['success']) && $statusResponse['success'] === true &&
                isset($statusResponse['registered']) && $statusResponse['registered'] === true;

            // Sync status to DB
            $currentApiStatus = $isTerminalConnected ? 'online' : 'offline';
            if ($store->pos_terminal_status !== $currentApiStatus) {
                $store->update(['pos_terminal_status' => $currentApiStatus]);
            }
        }

        return view('settings.quick_pos_page', compact('settings', 'store', 'isTerminalConnected'));
    }

    public function update(Request $request)
    {
        $settings = QuickPosSetting::first();

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'pos_terminal_id' => 'nullable|string|max:255',
            'pos_agent_secret' => 'nullable|string|max:255',
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
            'auto_print_receipt' => $request->has('auto_print_receipt'),
        ];

        $settings->update($settingsData);

        // Update POS credentials if provided in the exact same form
        $storeUpdates = [];
        if ($request->has('pos_terminal_id')) $storeUpdates['pos_terminal_id'] = $request->pos_terminal_id;
        if ($request->has('pos_agent_secret')) $storeUpdates['pos_agent_secret'] = $request->pos_agent_secret;
        if ($request->has('pos_hardware_url')) $storeUpdates['pos_hardware_url'] = $request->pos_hardware_url;

        if (!empty($storeUpdates)) {
            StoreDetail::where('id', Auth::user()->store_id)->update($storeUpdates);
        }

        return back()->with('success', 'Quick POS settings updated successfully.');
    }

    public function connectToServer(Request $request, PosAgentService $posAgentService)
    {
        $store = StoreDetail::where('id', Auth::user()->store_id)->firstOrFail();

        if (!$store->pos_terminal_id) {
            return back()->with('error', 'Please define a POS Terminal ID first and save settings before connecting.');
        }

        // Hit the /api/terminal/register endpoint
        $response = $posAgentService->registerTerminal($store->pos_terminal_id);

        if ($response['success']) {
            $store->update(['pos_terminal_status' => 'online']);
            return back()->with('success', 'Terminal successfully registered and connected to the POS Agent.');
        }

        return back()->with('error', 'Failed to connect: ' . ($response['message'] ?? 'Unknown error'));
    }
}

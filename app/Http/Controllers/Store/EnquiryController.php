<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\WareNotification;
use App\Models\WareUser;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enquiries = Enquiry::latest()->paginate(10);
        return view('store.enquiries.index', compact('enquiries'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        
        // Mark as read when viewed
        if (!$enquiry->is_read) {
            $enquiry->update(['is_read' => true]);
        }
        
        return view('store.enquiries.show', compact('enquiry'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->delete();

        return redirect()->route('store.enquiries.index')->with('success', 'Enquiry deleted successfully.');
    }

    /**
     * Escalation tier 1: Store -> Warehouse. Notifies Warehouse's Super Admin
     * and VP Operations (the same "admins" group warehouse-side features
     * already notify) directly in their notification bell — the two apps
     * share the same database, so this write is immediately visible there.
     */
    public function escalate($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        if (!$enquiry->isNew()) {
            return back()->with('error', 'This enquiry has already been escalated.');
        }

        $enquiry->update([
            'status' => Enquiry::STATUS_ESCALATED_WAREHOUSE,
            'escalated_at' => now(),
        ]);

        $recipients = WareUser::whereHas('roles', function ($q) {
            $q->where('name', 'Super Admin')->orWhere('name', 'VP Operations');
        })->where('is_active', true)->get();

        foreach ($recipients as $recipient) {
            WareNotification::create([
                'user_id' => $recipient->id,
                'title' => 'Enquiry Escalated from Store',
                'message' => "Enquiry from {$enquiry->name} (\"{$enquiry->subject}\") escalated by " . (Auth::user()->name ?? 'a store admin') . '.',
                'type' => 'warning',
            ]);
        }

        return back()->with('success', 'Enquiry escalated to the Warehouse team.');
    }
}

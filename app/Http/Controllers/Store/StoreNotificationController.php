<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreNotification;
use Illuminate\Support\Facades\Auth;

class StoreNotificationController extends Controller
{
    public function index()
    {
        $notifications = StoreNotification::where('user_id', Auth::id())->where('store_id', Auth::user()->store_id)
            ->latest()
            ->paginate(15);

        return view('store.notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        $notification = StoreNotification::where('user_id', Auth::id())->where('store_id', Auth::user()->store_id)->findOrFail($id);
        $notification->markAsRead();

        // Redirect to the URL if present, otherwise back
        if ($notification->url) {
            return redirect($notification->url);
        }

        return back()->with('success', 'Marked as read');
    }

    public function markAllRead()
    {
        StoreNotification::where('user_id', Auth::id())->where('store_id', Auth::user()->store_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
    
    public function clearAll()
    {
        StoreNotification::where('user_id', Auth::id())->where('store_id', Auth::user()->store_id)->delete();
        return back()->with('success', 'All notifications cleared.');
    }
}
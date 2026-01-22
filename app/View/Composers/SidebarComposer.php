<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\StockRequest;
use App\Models\StoreStock;
use App\Models\RecallRequest;

class SidebarComposer
{
    public function compose(View $view)
    {
        $pendingRequestCount = StockRequest::where('store_id', auth()->user()->store_id)
            ->where('status', 'pending')->count();

        $pendingReceivedCount = StockRequest::where('store_id', auth()->user()->store_id)
            ->where('status', 'dispatched')->count();

        $pendingRecallCount = RecallRequest::where('store_id', auth()->user()->store_id)
            ->where('status', 'pending_store_approval')->count();

        view()->share(compact('pendingRequestCount', 'pendingReceivedCount', 'pendingRecallCount'));
    }
}

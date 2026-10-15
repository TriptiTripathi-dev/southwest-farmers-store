<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use Illuminate\Support\Facades\Log;

/**
 * Signed Approve / Reject link from the Area Manager email (ticket 32).
 * No login: the signature is the authorisation.
 */
class StoreRequestApprovalController extends Controller
{
    public function decide(int $id, string $decision)
    {
        $r = StockRequest::find($id);
        $number = $r ? ($r->request_number ?? '#' . $r->id) : '';

        if (!$r) {
            return $this->result(false, 'Not found', 'This store request no longer exists.');
        }
        if ($r->status !== StockRequest::STATUS_AWAITING_APPROVAL) {
            $state = $r->approved_at ? 'approved' : str_replace('_', ' ', $r->status);
            return $this->result(false, 'Already handled', "Store request {$number} is already {$state}. No change was made.");
        }

        if ($decision === 'approve') {
            // Pending + approved_at = approved; the warehouse queue picks it up from here.
            $r->update(['status' => StockRequest::STATUS_PENDING, 'approved_at' => now()]);
            Log::info('Store request approved from email', ['id' => $r->id]);
            return $this->result(true, 'Approved', "Store request {$number} is approved and has been sent to the warehouse.");
        }

        $r->update(['status' => StockRequest::STATUS_REJECTED, 'admin_note' => 'Rejected by the Area Manager from the approval email.']);
        Log::info('Store request rejected from email', ['id' => $r->id]);
        return $this->result(false, 'Rejected', "Store request {$number} was rejected.");
    }

    private function result(bool $ok, string $title, string $message)
    {
        return response()->view('emails.store-request-decision-result', compact('ok', 'title', 'message'));
    }
}

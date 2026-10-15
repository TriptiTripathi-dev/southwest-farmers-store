<?php

namespace App\Mail;

use App\Models\StockRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Sent when a store request is marked Reviewed (ticket 32).
 * Area Manager (VP field) gets Approve / Reject links; the GM gets the same
 * order details as a notification only.
 */
class StoreRequestApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public StockRequest $stockRequest, public bool $forApprover)
    {
    }

    public function build()
    {
        $r = $this->stockRequest->loadMissing(['store', 'department', 'items.product']);
        $approveUrl = $rejectUrl = null;
        // Relative signature: the store app sits behind Railway's proxy without trusted
        // proxies, so an https/host-bound signature would not validate.
        if ($this->forApprover) {
            $approveUrl = url(URL::temporarySignedRoute('store-requests.external-decision', now()->addDays(7), ['id' => $r->id, 'decision' => 'approve'], false));
            $rejectUrl = url(URL::temporarySignedRoute('store-requests.external-decision', now()->addDays(7), ['id' => $r->id, 'decision' => 'reject'], false));
        }

        return $this->subject(($this->forApprover ? 'Approval Required: ' : 'Order Submitted: ') . 'Store Request ' . ($r->request_number ?? '#' . $r->id))
            ->view('emails.store-request-approval', [
                'r' => $r,
                'forApprover' => $this->forApprover,
                'approveUrl' => $approveUrl,
                'rejectUrl' => $rejectUrl,
            ]);
    }
}

<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Mail\SupportTicketCreated;
use App\Mail\SupportTicketReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StoreSupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::forStore(Auth::user()->store_id)->latest()->paginate(10);
        return view('store.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('store.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'description' => 'required'
        ]);

        // SLA Calculation
        $slaHours = match($request->priority) {
            'critical' => 2,
            'high' => 8,
            'medium' => 24,
            default => 48
        };

        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-' . date('Ymd') . '-' . rand(100, 999),
            'store_id' => Auth::user()->store_id,
            'created_by_id' => Auth::id(),
            'created_by_type' => get_class(Auth::user()),
            'category' => $request->category,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'open',
            'sla_due_at' => Carbon::now()->addHours($slaHours),
        ]);

        // Notify Warehouse (Generic Support Email)
        Mail::to('support@warehouse.com')->send(new SupportTicketCreated($ticket));

        return redirect()->route('store.support.index')->with('success', 'Ticket created successfully.');
    }

    public function show($id)
    {
        $ticket = SupportTicket::forStore(Auth::user()->store_id)
            ->with(['messages' => function($q) {
                $q->where('is_internal', false); // Hide internal notes
            }])
            ->findOrFail($id);
            
        return view('store.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required']);
        $ticket = SupportTicket::forStore(Auth::user()->store_id)->findOrFail($id);

        $msg = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'sender_type' => get_class(Auth::user()),
            'message' => $request->message,
        ]);

        // Notify Assigned Staff or Warehouse
        $recipient = $ticket->assignedTo->email ?? 'support@warehouse.com';
        Mail::to($recipient)->send(new SupportTicketReplied($ticket, $msg));

        return back()->with('success', 'Reply sent.');
    }
}
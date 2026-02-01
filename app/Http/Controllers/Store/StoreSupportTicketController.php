<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\SupportAttachment; // Added Model
use App\Mail\SupportTicketCreated;
use App\Mail\SupportTicketReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StoreSupportTicketController extends Controller
{
    public function index(Request $request)
    {
        // 1. UPDATE: Added Filtering Logic
        $query = SupportTicket::forStore(Auth::user()->store_id)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(10)->withQueryString(); // Keep filter params in pagination links

        return view('store.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('store.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'description' => 'required|string'
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
        // Ideally fetch this from a setting or Admin User
        try {
            Mail::to('support@warehouse.com')->send(new SupportTicketCreated($ticket));
        } catch (\Exception $e) {
            // Log mail failure but don't stop the process
            \Log::error('Support Email Failed: ' . $e->getMessage());
        }

        return redirect()->route('store.support.index')->with('success', 'Ticket created successfully.');
    }

    public function show($id)
    {
        $ticket = SupportTicket::forStore(Auth::user()->store_id)
            ->with(['messages' => function($q) {
                $q->where('is_internal', false); // Hide internal notes
            }, 'messages.attachments']) // 2. UPDATE: Eager load attachments
            ->findOrFail($id);
            
        return view('store.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048' // Validation for files
        ]);

        $ticket = SupportTicket::forStore(Auth::user()->store_id)->findOrFail($id);

        // Create Message
        $msg = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'sender_type' => get_class(Auth::user()),
            'message' => $request->message,
            'is_internal' => false
        ]);

        // 3. UPDATE: Handle Attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', 'public');
                
                SupportAttachment::create([
                    'ticket_id' => $ticket->id,
                    'message_id' => $msg->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->extension(),
                    'uploaded_by_id' => Auth::id(),
                    'uploaded_by_type' => get_class(Auth::user()),
                ]);
            }
        }

        // 4. UPDATE: Auto-Reopen Ticket Logic
        // If ticket was waiting or resolved, and store replies, move it back to open/in_progress
        if (in_array($ticket->status, ['resolved', 'waiting', 'closed'])) {
            $ticket->update(['status' => 'in_progress']);
        }

        // Notify Assigned Staff or Warehouse
        $recipient = $ticket->assignedTo->email ?? 'support@warehouse.com';
        
        try {
            Mail::to($recipient)->send(new SupportTicketReplied($ticket, $msg));
        } catch (\Exception $e) {
             \Log::error('Support Reply Email Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Reply sent successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // GET /support/tickets
    public function index()
    {
        return Ticket::with('ticket_replies')->latest()->get();
    }

    // POST /support/tickets
    public function store(Request $request)
    {
        $ticket = Ticket::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'user_id' => auth()->id() ?? 1, // fallback for demo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'ticket' => $ticket
        ], 201);
    }

    // GET /support/tickets/{id}
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->load('ticket_replies');
        return response()->json($ticket);
    }

    // POST /support/tickets/{id}/reply
    public function reply(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
            'reply' => $reply
        ], 201);
    }
}

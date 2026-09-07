<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    public function show(Ticket $ticket)
    {
        // Pastikan user hanya bisa lihat tiket sendiri (atau admin)
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $ticket->load([
            'schedule.route',
            'schedule.vehicle',
            'seat',
            'pickupPoint',
            'dropoffPoint',
            'user',
        ]);

        // Generate QR code sebagai SVG string (tidak butuh ext-gd)
        $qrCode = QrCode::format('svg')
            ->size(250)
            ->errorCorrection('H')
            ->generate($ticket->qr_token);

        return view('tickets.show', compact('ticket', 'qrCode'));
    }

    public function history()
    {
        $tickets = Ticket::with(['schedule.route', 'seat'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.history', compact('tickets'));
    }

    public function requestRefund(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ticket->status !== 'paid') {
            return redirect()->back()->with('error', 'Hanya tiket dengan status LUNAS yang dapat diajukan refund/reschedule.');
        }

        $ticket->update(['status' => 'refund_requested']);

        return redirect()->back()->with('success', 'Permohonan refund/reschedule Anda telah berhasil dikirim ke Admin.');
    }
}

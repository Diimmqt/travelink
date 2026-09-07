<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\BoardingLog;
use Illuminate\Http\Request;

class BoardingController extends Controller
{
    public function index()
    {
        $recentLogs = BoardingLog::with(['ticket.schedule.route', 'ticket.seat', 'ticket.user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('admin.boarding.index', compact('recentLogs'));
    }

    public function validateToken(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $token = trim($request->qr_token);
        $ticket = Ticket::with(['schedule.route', 'schedule.vehicle', 'seat', 'user', 'pickupPoint', 'dropoffPoint'])
            ->where('qr_token', $token)
            ->first();

        if (!$ticket) {
            return redirect()->route('admin.boarding.index')->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'QR Code Token tidak ditemukan dalam sistem database.',
                'ticket' => null
            ]);
        }

        if ($ticket->status === 'boarded') {
            BoardingLog::create([
                'ticket_id' => $ticket->id,
                'admin_id' => auth()->id(),
                'scan_time' => now(),
                'result' => 'rejected',
            ]);

            return redirect()->route('admin.boarding.index')->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'Tiket ini SUDAH DIGUNAKAN (Boarded) sebelumnya.',
                'ticket' => $ticket
            ]);
        }

        if ($ticket->status !== 'paid') {
            BoardingLog::create([
                'ticket_id' => $ticket->id,
                'admin_id' => auth()->id(),
                'scan_time' => now(),
                'result' => 'rejected',
            ]);

            return redirect()->route('admin.boarding.index')->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'Tiket belum sah/lunas (Status saat ini: ' . strtoupper($ticket->status) . ').',
                'ticket' => $ticket
            ]);
        }

        // TIKET VALID & DITERIMA!
        $ticket->update(['status' => 'boarded']);

        BoardingLog::create([
            'ticket_id' => $ticket->id,
            'admin_id' => auth()->id(),
            'scan_time' => now(),
            'result' => 'accepted',
        ]);

        return redirect()->route('admin.boarding.index')->with('validation_result', [
            'success' => true,
            'title' => 'Diterima',
            'message' => 'Validasi Berhasil! Penumpang dipersilakan masuk armada.',
            'ticket' => $ticket
        ]);
    }
}

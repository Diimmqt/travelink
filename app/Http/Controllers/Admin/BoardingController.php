<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\BoardingLog;
use Illuminate\Http\Request;

class BoardingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('qr_token') || $request->filled('token')) {
            return $this->validateToken($request);
        }

        $recentLogs = BoardingLog::with(['ticket.schedule.route', 'ticket.seat', 'ticket.user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('admin.boarding.index', compact('recentLogs'));
    }

    public function validateToken(Request $request)
    {
        $input = trim($request->input('qr_token', $request->query('qr_token', $request->query('token', ''))));

        if (empty($input)) {
            return redirect()->route('admin.boarding.index')->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'Silakan masukkan kode tiket atau scan QR Code.',
                'ticket' => null
            ]);
        }

        // Jika scanner membaca format URL, ekstrak token dari query atau path
        $token = $input;
        if (filter_var($input, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($input);
            $query = [];
            parse_str($parsed['query'] ?? '', $query);
            if (!empty($query['qr_token'])) {
                $token = $query['qr_token'];
            } elseif (!empty($query['token'])) {
                $token = $query['token'];
            } else {
                $token = basename($parsed['path'] ?? '');
            }
        }

        $token = trim($token);
        $cleanToken = strtolower(str_replace(['-', ' '], '', $token));

        $ticket = Ticket::with(['schedule.route', 'schedule.vehicle', 'seat', 'user', 'pickupPoint', 'dropoffPoint'])
            ->where(function ($q) use ($token, $cleanToken) {
                $q->where('qr_token', $token)
                  ->orWhere('qr_token', strtolower($token))
                  ->orWhere('qr_token', 'LIKE', strtolower($token) . '%')
                  ->orWhereRaw("REPLACE(qr_token, '-', '') LIKE ?", [$cleanToken . '%']);
            })
            ->first();

        if (!$ticket) {
            return redirect()->route('admin.boarding.index')->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => "Kode unik atau QR Token '{$input}' tidak ditemukan dalam database.",
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

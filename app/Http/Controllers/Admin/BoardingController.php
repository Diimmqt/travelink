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
        $cleanAlphanumeric = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $token));

        // Validasi panjang minimum kode (minimal 8 karakter heksadesimal atau UUID) untuk mencegah blind guessing / brute force
        if (strlen($cleanAlphanumeric) < 8) {
            return redirect()->route('admin.boarding.index')->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'Kode tiket harus terdiri dari minimal 8 karakter kode unik atau kode QR lengkap.',
                'ticket' => null
            ]);
        }

        $query = Ticket::with(['schedule.route', 'schedule.vehicle', 'seat', 'user', 'pickupPoint', 'dropoffPoint']);

        if (\Illuminate\Support\Str::isUuid($token)) {
            // Pencocokan eksak UUID jika formatnya adalah full UUID
            $ticket = $query->where('qr_token', $token)->first();
        } elseif (strlen($cleanAlphanumeric) === 32) {
            // Full UUID tanpa tanda minus
            $ticket = $query->whereRaw("REPLACE(qr_token, '-', '') = ?", [$cleanAlphanumeric])->first();
        } else {
            // Pencocokan 8 karakter kode unik awal (hanya karakter heksadesimal yang sudah disanitasi)
            $hexPrefix = substr($cleanAlphanumeric, 0, 8);
            $ticket = $query->where('qr_token', 'LIKE', $hexPrefix . '%')->first();
        }

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

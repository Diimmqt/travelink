<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\BoardingLog;
use Illuminate\Http\Request;

/**
 * Controller untuk memvalidasi QR Code / kode unik tiket saat penumpang akan naik armada (boarding).
 * Digunakan baik oleh Admin maupun Kasir loket.
 */
class BoardingController extends Controller
{
    /**
     * Menentukan nama route tujuan redirect sesuai peran/prefix route pengguna (kasir atau admin).
     *
     * @param  \Illuminate\Http\Request  $request  Objek permintaan HTTP
     * @return string  Nama route tujuan redirect
     */
    protected function getRedirectRoute(Request $request): string
    {
        return ($request->routeIs('kasir.*') || (auth()->check() && auth()->user()->isKasir()))
            ? 'kasir.boarding.index'
            : 'admin.boarding.index';
    }

    /**
     * Menampilkan halaman pemindaian boarding beserta 15 riwayat scan terakhir.
     * Jika terdapat parameter qr_token/token di URL, langsung jalankan proses validasi.
     *
     * @param  \Illuminate\Http\Request  $request  Objek permintaan HTTP
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Jika scanner kamera membuka URL yang langsung membawa parameter token, segera validasi
        if ($request->filled('qr_token') || $request->filled('token')) {
            return $this->validateToken($request);
        }

        // Ambil 15 log pemindaian boarding terbaru beserta relasi tiket dan petugas yang memindai
        $recentLogs = BoardingLog::with(['ticket.schedule.route', 'ticket.seat', 'ticket.user', 'admin'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('admin.boarding.index', compact('recentLogs'));
    }

    /**
     * Memvalidasi kode tiket atau QR Token yang diinput/dipindai, mencatat log boarding,
     * dan mengubah status tiket menjadi 'boarded' jika sah.
     *
     * @param  \Illuminate\Http\Request  $request  Permintaan HTTP berisi qr_token atau token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateToken(Request $request)
    {
        // Tentukan route redirect sesuai role petugas (admin/kasir) dan ambil input kode
        $redirectRoute = $this->getRedirectRoute($request);
        $input = trim($request->input('qr_token', $request->query('qr_token', $request->query('token', ''))));

        // Tolak jika input kosong
        if (empty($input)) {
            return redirect()->route($redirectRoute)->with('validation_result', [
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

        // Bersihkan spasi dan ambil hanya karakter alfanumerik huruf kecil untuk pengecekan format
        $token = trim($token);
        $cleanAlphanumeric = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $token));

        // Validasi panjang minimum kode (minimal 8 karakter heksadesimal atau UUID) untuk mencegah blind guessing / brute force
        if (strlen($cleanAlphanumeric) < 8) {
            return redirect()->route($redirectRoute)->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'Kode tiket harus terdiri dari minimal 8 karakter kode unik atau kode QR lengkap.',
                'ticket' => null
            ]);
        }

        // Siapkan query pencarian tiket beserta seluruh relasi yang dibutuhkan untuk tampilan hasil scan
        $query = Ticket::with(['schedule.route', 'schedule.vehicle', 'seat', 'user', 'pickupPoint', 'dropoffPoint']);

        // Cari tiket berdasarkan format token: UUID penuh, 32 karakter tanpa strip, atau 8 karakter awalan
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

        // Jika tiket tidak ditemukan di database, kembalikan pesan penolakan
        if (!$ticket) {
            return redirect()->route($redirectRoute)->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => "Kode unik atau QR Token '{$input}' tidak ditemukan dalam database.",
                'ticket' => null
            ]);
        }

        // Tolak dan catat log jika tiket sudah pernah dipakai untuk boarding sebelumnya
        if ($ticket->status === 'boarded') {
            BoardingLog::create([
                'ticket_id' => $ticket->id,
                'admin_id' => auth()->id(),
                'scan_time' => now(),
                'result' => 'rejected',
            ]);

            return redirect()->route($redirectRoute)->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'Tiket ini SUDAH DIGUNAKAN (Boarded) sebelumnya.',
                'ticket' => $ticket
            ]);
        }

        // Tolak dan catat log jika status tiket bukan 'paid' (misalnya masih pending, expired, atau refunded)
        if ($ticket->status !== 'paid') {
            BoardingLog::create([
                'ticket_id' => $ticket->id,
                'admin_id' => auth()->id(),
                'scan_time' => now(),
                'result' => 'rejected',
            ]);

            return redirect()->route($redirectRoute)->with('validation_result', [
                'success' => false,
                'title' => 'Ditolak',
                'message' => 'Tiket belum sah/lunas (Status saat ini: ' . strtoupper($ticket->status) . ').',
                'ticket' => $ticket
            ]);
        }

        // TIKET VALID & DITERIMA!
        // Ubah status tiket menjadi 'boarded' dan catat log keberhasilan pemindaian
        $ticket->update(['status' => 'boarded']);

        BoardingLog::create([
            'ticket_id' => $ticket->id,
            'admin_id' => auth()->id(),
            'scan_time' => now(),
            'result' => 'accepted',
        ]);

        return redirect()->route($redirectRoute)->with('validation_result', [
            'success' => true,
            'title' => 'Diterima',
            'message' => 'Validasi Berhasil! Penumpang dipersilakan masuk armada.',
            'ticket' => $ticket
        ]);
    }
}

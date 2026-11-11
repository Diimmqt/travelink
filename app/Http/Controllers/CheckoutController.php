<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Seat;
use App\Models\PickupPoint;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Controller untuk menangani halaman ringkasan checkout dan proses pembayaran tiket melalui Midtrans.
 */
class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman ringkasan pesanan (checkout) sebelum pembayaran dilakukan.
     * Memvalidasi data booking dari session, memastikan jadwal, titik jemput/turun, dan kursi masih tersedia.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show()
    {
        // Mengambil data pemesanan sementara yang disimpan di session
        $booking = session('booking');

        // Jika data booking kosong atau penumpang/kursi belum dipilih, arahkan kembali ke pencarian jadwal
        if (!$booking || empty($booking['passengers']) || empty($booking['seat_ids'])) {
            return redirect()->route('schedules.search')
                ->with('error', 'Data pemesanan tidak ditemukan. Silakan mulai pencarian kembali.');
        }

        // Mengambil data jadwal beserta relasi rute dan kendaraan berdasarkan schedule_id dari session
        $schedule = Schedule::with(['route', 'vehicle'])->find($booking['schedule_id']);
        // Jika jadwal tidak ditemukan di database, hapus session booking dan kembalikan ke halaman pencarian
        if (!$schedule) {
            session()->forget('booking');
            return redirect()->route('schedules.search')
                ->with('error', 'Data jadwal tidak ditemukan.');
        }

        // Mengambil daftar kursi yang dipilih sesuai jadwal, titik jemput, dan titik turun
        $seats    = Seat::whereIn('id', $booking['seat_ids'])->where('schedule_id', $schedule->id)->orderBy('id')->get();
        $pickup   = PickupPoint::find($booking['pickup_point_id']);
        $dropoff  = PickupPoint::find($booking['dropoff_point_id']);

        // Pastikan jumlah kursi yang ditemukan sama dengan jumlah penumpang yang didaftarkan
        if ($seats->count() !== count($booking['passengers'])) {
            session()->forget('booking');
            return redirect()->route('schedules.detail', $booking['schedule_id'])
                ->with('error', 'Pilihan kursi tidak lengkap atau tidak valid. Silakan pilih kembali.');
        }

        // Check if any seat is booked by another transaction
        // Periksa setiap kursi untuk memastikan belum dipesan (booked) oleh transaksi pengguna lain
        foreach ($seats as $seat) {
            if ($seat->effective_status === 'booked') {
                session()->forget('booking');
                return redirect()->route('schedules.detail', $booking['schedule_id'])
                    ->with('error', "Kursi {$seat->nomor_kursi} telah dipesan. Silakan pilih kursi kembali.");
            }
        }

        // Menghitung total harga tiket berdasarkan harga rute dikalikan jumlah penumpang
        $totalPrice = $schedule->route->harga * count($booking['passengers']);

        // Mengirimkan seluruh data checkout dan konfigurasi Midtrans ke tampilan view
        return view('checkout.show', [
            'booking'    => $booking,
            'schedule'   => $schedule,
            'seats'      => $seats,
            'pickup'     => $pickup,
            'dropoff'    => $dropoff,
            'totalPrice' => $totalPrice,
            'clientKey'  => config('midtrans.client_key'),
            'snapUrl'    => config('midtrans.snap_url'),
        ]);
    }

    /**
     * Memproses checkout dengan membuat transaksi & tiket di database lalu meminta Snap Token ke Midtrans.
     * Mendukung permintaan AJAX/JSON maupun form standar, serta mendukung percobaan ulang (retry) pembayaran pending.
     *
     * @param  \Illuminate\Http\Request        $request   Objek permintaan HTTP dari pengguna
     * @param  \App\Services\MidtransService   $midtrans  Layanan integrasi payment gateway Midtrans
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function process(Request $request, MidtransService $midtrans)
    {
        // Ambil data pemesanan dari session
        $booking = session('booking');

        // Validasi awal: pastikan data booking, daftar penumpang, dan kursi tersedia di session
        if (!$booking || empty($booking['passengers']) || empty($booking['seat_ids'])) {
            // Jika request berupa AJAX/JSON, kirim respons error dalam format JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Data pemesanan tidak valid. Silakan ulangi pencarian.',
                    'redirect' => route('schedules.search'),
                ], 422);
            }
            return redirect()->route('schedules.search')
                ->with('error', 'Data pemesanan tidak valid.');
        }

        try {
            // Cek apakah pengguna sudah memiliki transaksi berstatus pending di session (misal saat klik ulang Bayar)
            $pendingTransactionId = session('pending_transaction_id');
            $existingTransaction = $pendingTransactionId ? Transaction::with('tickets')->find($pendingTransactionId) : null;

            // Jika transaksi pending masih ada, gunakan ulang transaksi tersebut tanpa membuat data tiket ganda
            if ($existingTransaction && $existingTransaction->status === 'pending') {
                // Perbarui idempotency_key agar Midtrans menerima order_id baru saat retry
                $existingTransaction->update([
                    'idempotency_key' => 'TRX-' . strtoupper(Str::random(16)),
                ]);
                // Buat Snap Token baru untuk transaksi yang sudah ada
                $token = $midtrans->createSnapToken($existingTransaction);
                $firstTicket = $existingTransaction->tickets->first() ?? $existingTransaction->ticket;
                $ticketId = $firstTicket ? $firstTicket->id : 0;
            } else {
                // Alur transaksi baru dijalankan dalam DB::transaction agar semua perubahan bersifat atomik (berhasil semua atau batal semua)
                $snapResult = DB::transaction(function () use ($booking, $midtrans) {
                    // 1. Ambil data jadwal, jumlah penumpang, dan daftar ID kursi
                    $schedule = Schedule::findOrFail($booking['schedule_id']);
                    $passengerCount = count($booking['passengers']);
                    $seatIds = $booking['seat_ids'];

                    // Lock all seat rows for update, strictly belonging to this schedule
                    // Mengunci baris data kursi di database (pessimistic lock) agar tidak direbut user lain secara bersamaan
                    $seats = Seat::whereIn('id', $seatIds)
                        ->where('schedule_id', $schedule->id)
                        ->lockForUpdate()
                        ->get();

                    // Pastikan semua kursi yang dipilih valid dan sesuai dengan jadwal keberangkatan
                    if ($seats->count() !== $passengerCount) {
                        throw new \Exception('Satu atau lebih kursi yang dipilih tidak valid untuk jadwal keberangkatan ini.');
                    }

                    // Expire stale tickets for this schedule
                    // Tandai tiket pending yang sudah lewat 10 menit sebagai expired agar kursinya bisa dipakai lagi
                    Ticket::where('schedule_id', $schedule->id)
                        ->where('status', 'pending')
                        ->where('created_at', '<', now()->subMinutes(10))
                        ->update(['status' => 'expired']);

                    // Verify no other active ticket exists for these seats
                    // Periksa apakah ada tiket aktif (sudah dibayar, sudah naik, atau pending < 10 menit) pada kursi yang sama
                    $occupied = Ticket::whereIn('seat_id', $seatIds)
                        ->where(function ($query) {
                            $query->whereIn('status', ['paid', 'boarded'])
                                  ->orWhere(function ($q) {
                                      $q->where('status', 'pending')
                                        ->where('created_at', '>=', now()->subMinutes(10));
                                  });
                        })
                        ->exists();

                    // Jika salah satu kursi sudah terpakai transaksi lain, batalkan proses dengan melempar exception
                    if ($occupied) {
                        throw new \Exception('Satu atau lebih kursi telah dipesan transaksi lain. Silakan pilih kursi kembali.');
                    }

                    // Hitung total nominal pembayaran
                    $totalAmount = $schedule->route->harga * $passengerCount;

                    // 1. Buat Transaction induk
                    // Simpan data transaksi utama dengan status awal pending dan kunci unik order_id Midtrans
                    $transaction = Transaction::create([
                        'user_id'          => Auth::id(),
                        'ticket_id'        => null,
                        'payment_method'   => null,
                        'amount'           => $totalAmount,
                        'status'           => 'pending',
                        'idempotency_key'  => 'TRX-' . strtoupper(Str::random(16)),
                    ]);

                    $createdTickets = [];

                    // 2. Buat Tiket untuk setiap penumpang
                    // Lakukan perulangan untuk membuat data tiket masing-masing penumpang dan mengunci kursinya selama 10 menit
                    foreach ($booking['passengers'] as $idx => $passenger) {
                        $seatId = $seatIds[$idx] ?? $seatIds[0];
                        $seat = $seats->firstWhere('id', $seatId);

                        // Buat record tiket baru dengan qr_token UUID unik untuk proses boarding
                        $ticket = Ticket::create([
                            'schedule_id'      => $schedule->id,
                            'seat_id'          => $seatId,
                            'user_id'          => Auth::id(),
                            'transaction_id'   => $transaction->id,
                            'pickup_point_id'  => $booking['pickup_point_id'],
                            'dropoff_point_id' => $booking['dropoff_point_id'],
                            'nama_penumpang'   => $passenger['nama_lengkap'],
                            'nik'              => $passenger['nik'],
                            'qr_token'         => (string) Str::uuid(),
                            'status'           => 'pending',
                        ]);

                        // Kunci sementara kursi selama 10 menit menunggu pembayaran diselesaikan
                        $seat->update([
                            'status'       => 'locked',
                            'locked_until' => now()->addMinutes(10),
                        ]);

                        $createdTickets[] = $ticket;
                    }

                    // Set primary ticket_id on transaction for backwards compatibility
                    // Simpan ID tiket pertama ke kolom ticket_id transaksi untuk kompatibilitas struktur lama
                    $transaction->update([
                        'ticket_id' => $createdTickets[0]->id,
                    ]);

                    // 3. Buat Snap token Midtrans
                    // Meminta token pop-up pembayaran dari Midtrans dan mengembalikan hasilnya dari transaksi DB
                    return [
                        'token'          => $midtrans->createSnapToken($transaction),
                        'ticket_id'      => $createdTickets[0]->id,
                        'transaction_id' => $transaction->id,
                    ];
                });

                // Simpan ID transaksi dan tiket pending ke session agar bisa digunakan jika user menutup pop-up lalu membayar ulang
                $token = $snapResult['token'];
                $ticketId = $snapResult['ticket_id'];
                session()->put('pending_transaction_id', $snapResult['transaction_id']);
                session()->put('pending_ticket_id', $ticketId);
            }

            // Jika dipanggil lewat JavaScript (AJAX/Fetch), kembalikan Snap Token dalam respons JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'    => true,
                    'snap_token' => $token,
                    'ticket_id'  => $ticketId,
                ]);
            }

            // Jika bukan AJAX, tampilkan halaman pembayaran khusus
            return view('checkout.payment', [
                'snapToken'  => $token,
                'clientKey'  => config('midtrans.client_key'),
                'snapUrl'    => config('midtrans.snap_url'),
                'ticketId'   => $ticketId,
            ]);

        } catch (\Exception $e) {
            // Tangani error selama proses checkout/transaksi dan kembalikan pesan error sesuai tipe request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->route('schedules.detail', $booking['schedule_id'])
                ->with('error', $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk mengelola persetujuan dan penolakan pengajuan refund tiket oleh Admin.
 */
class RefundController extends Controller
{
    /**
     * Menampilkan daftar tiket yang mengajukan refund atau sudah di-refund sesuai filter status.
     *
     * @param  \Illuminate\Http\Request  $request  Permintaan HTTP berisi filter 'status' (default: refund_requested)
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil filter status dari query string; default menampilkan pengajuan yang masih menunggu persetujuan
        $status = $request->input('status', 'refund_requested');

        // Muat relasi jadwal, kendaraan, kursi, user, dan transaksi agar tidak terjadi query berulang (N+1) di view
        $query = Ticket::with(['schedule.route', 'schedule.vehicle', 'seat', 'user', 'transaction']);

        // Saring tiket berdasarkan tab status yang dipilih admin
        if ($status === 'refund_requested') {
            $query->where('status', 'refund_requested');
        } else if ($status === 'refunded') {
            $query->where('status', 'refunded');
        } else {
            $query->whereIn('status', ['refund_requested', 'refunded']);
        }

        $tickets = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.refunds.index', compact('tickets', 'status'));
    }

    /**
     * Menyetujui pengajuan refund: mengubah status tiket dan transaksi menjadi 'refunded',
     * serta mengembalikan status kursi menjadi 'available' agar dapat dipesan penumpang lain.
     *
     * @param  \App\Models\Ticket  $ticket  Tiket yang pengajuan refund-nya disetujui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Ticket $ticket)
    {
        // Pastikan tiket memang berstatus pengajuan refund atau sudah dibayar sebelum diproses
        if ($ticket->status !== 'refund_requested' && $ticket->status !== 'paid') {
            return redirect()->back()->with('error', 'Tiket ini tidak berada pada status pengajuan refund.');
        }

        // Jalankan perubahan status tiket, transaksi, dan pembebasan kursi secara atomik dalam DB::transaction
        DB::transaction(function () use ($ticket) {
            // Update ticket status
            $ticket->update(['status' => 'refunded']);

            // Update transaction status
            Transaction::where('ticket_id', $ticket->id)->update(['status' => 'refunded']);

            // Release seat back to available
            if ($ticket->seat_id) {
                Seat::where('id', $ticket->seat_id)->update([
                    'status' => 'available',
                    'locked_until' => null
                ]);
            }
        });

        return redirect()->route('admin.refunds.index')->with('success', 'Permohonan refund berhasil disetujui. Status tiket diubah menjadi REFUNDED dan kursi telah dibebaskan.');
    }

    /**
     * Menolak pengajuan refund dan mengembalikan status tiket menjadi lunas ('paid').
     *
     * @param  \App\Models\Ticket  $ticket  Tiket yang pengajuan refund-nya ditolak
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Ticket $ticket)
    {
        // Hanya tiket dengan status 'refund_requested' yang dapat ditolak
        if ($ticket->status !== 'refund_requested') {
            return redirect()->back()->with('error', 'Tiket ini tidak berada pada status pengajuan refund.');
        }

        // Kembalikan status tiket menjadi 'paid' sehingga tiket tetap sah digunakan penumpang
        $ticket->update(['status' => 'paid']);

        return redirect()->route('admin.refunds.index')->with('success', 'Permohonan refund berhasil ditolak. Status tiket dikembalikan ke LUNAS.');
    }
}

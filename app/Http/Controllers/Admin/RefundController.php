<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'refund_requested');

        $query = Ticket::with(['schedule.route', 'schedule.vehicle', 'seat', 'user', 'transaction']);

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

    public function approve(Ticket $ticket)
    {
        if ($ticket->status !== 'refund_requested' && $ticket->status !== 'paid') {
            return redirect()->back()->with('error', 'Tiket ini tidak berada pada status pengajuan refund.');
        }

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

    public function reject(Ticket $ticket)
    {
        if ($ticket->status !== 'refund_requested') {
            return redirect()->back()->with('error', 'Tiket ini tidak berada pada status pengajuan refund.');
        }

        $ticket->update(['status' => 'paid']);

        return redirect()->route('admin.refunds.index')->with('success', 'Permohonan refund berhasil ditolak. Status tiket dikembalikan ke LUNAS.');
    }
}

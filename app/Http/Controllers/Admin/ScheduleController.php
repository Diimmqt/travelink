<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller CRUD untuk mengelola jadwal keberangkatan armada beserta pembuatan kursi otomatisnya.
 */
class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal keberangkatan dengan filter rute dan status perjalanan.
     *
     * @param  \Illuminate\Http\Request  $request  Permintaan HTTP yang dapat berisi filter route_id dan status
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Siapkan query jadwal beserta relasi rute, armada, dan daftar kursi
        $query = Schedule::with(['route', 'vehicle', 'seats']);

        // Terapkan filter berdasarkan rute jika dipilih
        if ($request->has('route_id') && $request->route_id != '') {
            $query->where('route_id', $request->route_id);
        }

        // Terapkan filter berdasarkan status jadwal jika dipilih
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Urutkan dari jadwal keberangkatan terbaru, paginasi 10 baris, dan ambil semua rute untuk dropdown filter
        $schedules = $query->orderBy('waktu_berangkat', 'desc')->paginate(10)->withQueryString();
        $routes = Route::all();

        return view('admin.schedules.index', compact('schedules', 'routes'));
    }

    /**
     * Menampilkan formulir pembuatan jadwal keberangkatan baru beserta daftar rute dan armada.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $routes = Route::all();
        $vehicles = Vehicle::all();
        return view('admin.schedules.create', compact('routes', 'vehicles'));
    }

    /**
     * Memvalidasi data jadwal, memastikan titik jemput & turun rute tersedia, lalu menyimpan jadwal
     * sekaligus membuat seluruh kursi armada secara otomatis dalam transaksi database.
     *
     * @param  \Illuminate\Http\Request  $request  Data input form pembuatan jadwal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input jadwal; waktu berangkat wajib di masa depan (after:now)
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'waktu_berangkat' => 'required|date|after:now',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ], [
            'waktu_berangkat.after' => 'Waktu keberangkatan harus setelah waktu saat ini.',
        ]);

        // Ambil rute terkait dan pastikan kota asal memiliki titik jemput serta kota tujuan memiliki titik turun
        $route = Route::findOrFail($request->route_id);
        $hasPickup = \App\Models\PickupPoint::where(function($q) use ($route) {
            $q->where('kota', $route->kota_asal)
              ->orWhere('route_id', $route->id);
        })->whereIn('tipe', ['jemput', 'keduanya'])->exists();

        $hasDropoff = \App\Models\PickupPoint::where(function($q) use ($route) {
            $q->where('kota', $route->kota_tujuan)
              ->orWhere('route_id', $route->id);
        })->whereIn('tipe', ['turun', 'keduanya'])->exists();

        // Jika titik jemput atau titik turun belum tersedia, kembalikan ke form dengan pesan error yang spesifik
        if (!$hasPickup || !$hasDropoff) {
            $missing = [];
            if (!$hasPickup) $missing[] = "titik jemput di {$route->kota_asal}";
            if (!$hasDropoff) $missing[] = "titik turun di {$route->kota_tujuan}";
            return back()->withInput()->withErrors([
                'route_id' => 'Belum tersedia ' . implode(' dan ', $missing) . '. Silakan tambahkan di menu Titik Jemput/Turun.'
            ]);
        }

        // Simpan jadwal dan buat kursi sebanyak kapasitas kendaraan dalam DB::transaction agar konsisten
        DB::transaction(function () use ($validated) {
            $schedule = Schedule::create($validated);
            $vehicle = Vehicle::findOrFail($schedule->vehicle_id);

            // AUTOMATICALLY GENERATE SEATS CORRESPONDING TO VEHICLE'S CAPACITY (Format: A1, A2, ...)
            for ($i = 1; $i <= $vehicle->kapasitas_kursi; $i++) {
                Seat::create([
                    'schedule_id' => $schedule->id,
                    'nomor_kursi' => 'A' . $i,
                    'status' => 'available',
                ]);
            }
        });

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal keberangkatan baru dan seluruh kursi armada (' . $route->kota_asal . ' - ' . $route->kota_tujuan . ') berhasil dibuat.');
    }

    /**
     * Menampilkan formulir edit untuk jadwal keberangkatan yang dipilih.
     *
     * @param  \App\Models\Schedule  $schedule  Model jadwal yang akan diubah
     * @return \Illuminate\View\View
     */
    public function edit(Schedule $schedule)
    {
        $routes = Route::all();
        $vehicles = Vehicle::all();
        return view('admin.schedules.edit', compact('schedule', 'routes', 'vehicles'));
    }

    /**
     * Memvalidasi dan memperbarui data jadwal keberangkatan di database.
     *
     * @param  \Illuminate\Http\Request  $request   Data perubahan jadwal
     * @param  \App\Models\Schedule      $schedule  Model jadwal yang diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'waktu_berangkat' => 'required|date',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal keberangkatan berhasil diperbarui.');
    }

    /**
     * Menghapus data jadwal keberangkatan dari database.
     *
     * @param  \App\Models\Schedule  $schedule  Model jadwal yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal keberangkatan berhasil dihapus.');
    }
}

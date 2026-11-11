<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupPoint;
use App\Models\Route;
use Illuminate\Http\Request;

/**
 * Controller CRUD untuk mengelola titik penjemputan (jemput) dan penurunan (turun) penumpang di setiap kota.
 */
class PickupPointController extends Controller
{
    /**
     * Menampilkan daftar titik jemput/turun beserta filter kota dan tipe titik.
     *
     * @param  \Illuminate\Http\Request  $request  Permintaan HTTP yang dapat berisi filter 'kota' dan 'tipe'
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mulai query builder untuk model PickupPoint
        $query = PickupPoint::query();

        // Filter berdasarkan nama kota jika dipilih oleh admin
        if ($request->filled('kota')) {
            $query->where('kota', $request->kota);
        }

        // Filter berdasarkan tipe ('jemput' atau 'turun'), namun titik bertipe 'keduanya' selalu ikut disertakan
        if ($request->filled('tipe')) {
            $query->where(function($q) use ($request) {
                $q->where('tipe', $request->tipe)
                  ->orWhere('tipe', 'keduanya');
            });
        }

        // Urutkan berdasarkan kota lalu nama titik, paginasi 15 data, dan pertahankan query filter di link halaman
        $pickupPoints = $query->orderBy('kota')->orderBy('nama_titik')->paginate(15)->withQueryString();

        // Get list of all distinct cities
        // Gabungkan daftar kota unik dari tabel routes (asal & tujuan) serta tabel pickup_points untuk opsi dropdown filter
        $cities = Route::select('kota_asal as kota')
            ->union(Route::select('kota_tujuan as kota'))
            ->union(PickupPoint::select('kota')->whereNotNull('kota'))
            ->pluck('kota')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.pickup_points.index', compact('pickupPoints', 'cities'));
    }

    /**
     * Menampilkan formulir tambah titik penjemputan/penurunan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Ambil daftar seluruh kota unik dari rute dan titik jemput yang sudah ada sebagai pilihan dropdown
        $cities = Route::select('kota_asal as kota')
            ->union(Route::select('kota_tujuan as kota'))
            ->union(PickupPoint::select('kota')->whereNotNull('kota'))
            ->pluck('kota')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.pickup_points.create', compact('cities'));
    }

    /**
     * Memvalidasi dan menyimpan titik penjemputan/penurunan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request  Data input form tambah titik
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi kolom wajib dan batasi nilai tipe hanya pada jemput, turun, atau keduanya
        $validated = $request->validate([
            'kota' => 'required|string|max:100',
            'nama_titik' => 'required|string|max:150',
            'alamat' => 'required|string',
            'tipe' => 'required|in:jemput,turun,keduanya',
        ]);

        PickupPoint::create($validated);

        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik penjemputan/penurunan berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit untuk titik jemput/turun yang dipilih.
     *
     * @param  \App\Models\PickupPoint  $pickupPoint  Data titik yang akan diubah
     * @return \Illuminate\View\View
     */
    public function edit(PickupPoint $pickupPoint)
    {
        // Ambil daftar kota unik untuk dropdown pilihan kota pada form edit
        $cities = Route::select('kota_asal as kota')
            ->union(Route::select('kota_tujuan as kota'))
            ->union(PickupPoint::select('kota')->whereNotNull('kota'))
            ->pluck('kota')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.pickup_points.edit', compact('pickupPoint', 'cities'));
    }

    /**
     * Memvalidasi dan memperbarui data titik penjemputan/penurunan di database.
     *
     * @param  \Illuminate\Http\Request  $request      Data perubahan dari form edit
     * @param  \App\Models\PickupPoint   $pickupPoint  Model titik yang diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PickupPoint $pickupPoint)
    {
        $validated = $request->validate([
            'kota' => 'required|string|max:100',
            'nama_titik' => 'required|string|max:150',
            'alamat' => 'required|string',
            'tipe' => 'required|in:jemput,turun,keduanya',
        ]);

        $pickupPoint->update($validated);

        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik penjemputan/penurunan berhasil diperbarui.');
    }

    /**
     * Menghapus data titik penjemputan/penurunan dari database.
     *
     * @param  \App\Models\PickupPoint  $pickupPoint  Model titik yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PickupPoint $pickupPoint)
    {
        $pickupPoint->delete();
        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik berhasil dihapus.');
    }
}

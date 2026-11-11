<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

/**
 * Controller CRUD untuk mengelola master data rute perjalanan antar kota (asal, tujuan, tarif, dan durasi).
 */
class RouteController extends Controller
{
    /**
     * Menampilkan daftar seluruh rute perjalanan beserta jumlah jadwal yang terkait (10 data per halaman).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Hitung jumlah jadwal per rute menggunakan withCount('schedules')
        $routes = Route::withCount('schedules')->paginate(10);
        return view('admin.routes.index', compact('routes'));
    }

    /**
     * Menampilkan formulir untuk menambahkan rute perjalanan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.routes.create');
    }

    /**
     * Memvalidasi dan menyimpan data rute perjalanan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request  Data input form rute baru
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi kota asal/tujuan, harga minimal 0, dan estimasi durasi minimal 1 menit
        $validated = $request->validate([
            'kota_asal' => 'required|string|max:100',
            'kota_tujuan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'estimasi_durasi_menit' => 'required|integer|min:1',
        ]);

        Route::create($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Rute perjalanan berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit untuk rute perjalanan yang dipilih.
     *
     * @param  \App\Models\Route  $route  Data rute yang akan diubah
     * @return \Illuminate\View\View
     */
    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    /**
     * Memvalidasi dan memperbarui data rute perjalanan di database.
     *
     * @param  \Illuminate\Http\Request  $request  Data perubahan rute
     * @param  \App\Models\Route         $route    Model rute yang diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'kota_asal' => 'required|string|max:100',
            'kota_tujuan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'estimasi_durasi_menit' => 'required|integer|min:1',
        ]);

        $route->update($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Rute perjalanan berhasil diperbarui.');
    }

    /**
     * Menghapus data rute perjalanan dari database.
     *
     * @param  \App\Models\Route  $route  Model rute yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')->with('success', 'Rute perjalanan berhasil dihapus.');
    }
}

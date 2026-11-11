<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

/**
 * Controller CRUD untuk mengelola data armada kendaraan (jenis, plat nomor, dan kapasitas kursi).
 */
class VehicleController extends Controller
{
    /**
     * Menampilkan daftar seluruh armada kendaraan beserta jumlah jadwal yang menggunakannya.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vehicles = Vehicle::withCount('schedules')->paginate(10);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Menampilkan formulir untuk menambahkan data armada kendaraan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.vehicles.create');
    }

    /**
     * Memvalidasi dan menyimpan data armada kendaraan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request  Data input form tambah armada
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi data armada; plat nomor harus unik dan kapasitas kursi dibatasi 1 s.d. 50
        $validated = $request->validate([
            'jenis' => 'required|string|max:100',
            'plat_nomor' => 'required|string|max:20|unique:vehicles,plat_nomor',
            'kapasitas_kursi' => 'required|integer|min:1|max:50',
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Data armada kendaraan berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit untuk armada kendaraan yang dipilih.
     *
     * @param  \App\Models\Vehicle  $vehicle  Data armada yang akan diubah
     * @return \Illuminate\View\View
     */
    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    /**
     * Memvalidasi dan memperbarui data armada kendaraan di database.
     *
     * @param  \Illuminate\Http\Request  $request  Data perubahan armada
     * @param  \App\Models\Vehicle       $vehicle  Model armada yang diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Pengecualian ID kendaraan saat ini pada aturan unique agar plat nomor lama tetap valid
        $validated = $request->validate([
            'jenis' => 'required|string|max:100',
            'plat_nomor' => 'required|string|max:20|unique:vehicles,plat_nomor,' . $vehicle->id,
            'kapasitas_kursi' => 'required|integer|min:1|max:50',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Data armada kendaraan berhasil diperbarui.');
    }

    /**
     * Menghapus data armada kendaraan dari database.
     *
     * @param  \App\Models\Vehicle  $vehicle  Model armada yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Armada kendaraan berhasil dihapus.');
    }
}

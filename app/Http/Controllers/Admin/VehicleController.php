<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::withCount('schedules')->paginate(10);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|max:100',
            'plat_nomor' => 'required|string|max:20|unique:vehicles,plat_nomor',
            'kapasitas_kursi' => 'required|integer|min:1|max:50',
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Data armada kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|max:100',
            'plat_nomor' => 'required|string|max:20|unique:vehicles,plat_nomor,' . $vehicle->id,
            'kapasitas_kursi' => 'required|integer|min:1|max:50',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Data armada kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Armada kendaraan berhasil dihapus.');
    }
}

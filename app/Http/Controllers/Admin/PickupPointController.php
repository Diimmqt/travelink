<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupPoint;
use App\Models\Route;
use Illuminate\Http\Request;

class PickupPointController extends Controller
{
    public function index(Request $request)
    {
        $query = PickupPoint::with('route');

        if ($request->has('route_id') && $request->route_id != '') {
            $query->where('route_id', $request->route_id);
        }

        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        $pickupPoints = $query->paginate(15)->withQueryString();
        $routes = Route::all();

        return view('admin.pickup_points.index', compact('pickupPoints', 'routes'));
    }

    public function create()
    {
        $routes = Route::all();
        return view('admin.pickup_points.create', compact('routes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'nama_titik' => 'required|string|max:150',
            'alamat' => 'required|string',
            'tipe' => 'required|in:jemput,turun',
        ]);

        PickupPoint::create($validated);

        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik penjemputan/penurunan berhasil ditambahkan.');
    }

    public function edit(PickupPoint $pickupPoint)
    {
        $routes = Route::all();
        return view('admin.pickup_points.edit', compact('pickupPoint', 'routes'));
    }

    public function update(Request $request, PickupPoint $pickupPoint)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'nama_titik' => 'required|string|max:150',
            'alamat' => 'required|string',
            'tipe' => 'required|in:jemput,turun',
        ]);

        $pickupPoint->update($validated);

        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik penjemputan/penurunan berhasil diperbarui.');
    }

    public function destroy(PickupPoint $pickupPoint)
    {
        $pickupPoint->delete();
        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik berhasil dihapus.');
    }
}

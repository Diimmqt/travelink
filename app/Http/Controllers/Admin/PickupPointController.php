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
        $query = PickupPoint::query();

        if ($request->filled('kota')) {
            $query->where('kota', $request->kota);
        }

        if ($request->filled('tipe')) {
            $query->where(function($q) use ($request) {
                $q->where('tipe', $request->tipe)
                  ->orWhere('tipe', 'keduanya');
            });
        }

        $pickupPoints = $query->orderBy('kota')->orderBy('nama_titik')->paginate(15)->withQueryString();

        // Get list of all distinct cities
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

    public function create()
    {
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kota' => 'required|string|max:100',
            'nama_titik' => 'required|string|max:150',
            'alamat' => 'required|string',
            'tipe' => 'required|in:jemput,turun,keduanya',
        ]);

        PickupPoint::create($validated);

        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik penjemputan/penurunan berhasil ditambahkan.');
    }

    public function edit(PickupPoint $pickupPoint)
    {
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

    public function destroy(PickupPoint $pickupPoint)
    {
        $pickupPoint->delete();
        return redirect()->route('admin.pickup-points.index')->with('success', 'Titik berhasil dihapus.');
    }
}

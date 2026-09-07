<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::withCount(['schedules', 'pickupPoints'])->paginate(10);
        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.routes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kota_asal' => 'required|string|max:100',
            'kota_tujuan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'estimasi_durasi_menit' => 'required|integer|min:1',
        ]);

        Route::create($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Rute perjalanan berhasil ditambahkan.');
    }

    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

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

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')->with('success', 'Rute perjalanan berhasil dihapus.');
    }
}

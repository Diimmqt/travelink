<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['route', 'vehicle', 'seats']);

        if ($request->has('route_id') && $request->route_id != '') {
            $query->where('route_id', $request->route_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('waktu_berangkat', 'desc')->paginate(10)->withQueryString();
        $routes = Route::all();

        return view('admin.schedules.index', compact('schedules', 'routes'));
    }

    public function create()
    {
        $routes = Route::all();
        $vehicles = Vehicle::all();
        return view('admin.schedules.create', compact('routes', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'waktu_berangkat' => 'required|date|after:now',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        DB::transaction(function () use ($validated) {
            $schedule = Schedule::create($validated);
            $vehicle = Vehicle::findOrFail($schedule->vehicle_id);

            // AUTOMATICALLY GENERATE SEATS CORRESPONDING TO VEHICLE'S CAPACITY
            for ($i = 1; $i <= $vehicle->kapasitas_kursi; $i++) {
                Seat::create([
                    'schedule_id' => $schedule->id,
                    'nomor_kursi' => (string) $i,
                    'status' => 'available',
                ]);
            }
        });

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal keberangkatan baru dan seluruh kursi armada berhasil dibuat.');
    }

    public function edit(Schedule $schedule)
    {
        $routes = Route::all();
        $vehicles = Vehicle::all();
        return view('admin.schedules.edit', compact('schedule', 'routes', 'vehicles'));
    }

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

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal keberangkatan berhasil dihapus.');
    }
}

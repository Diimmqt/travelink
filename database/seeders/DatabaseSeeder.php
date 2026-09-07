<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Route;
use App\Models\PickupPoint;
use App\Models\Vehicle;
use App\Models\Schedule;
use App\Models\Seat;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::factory()->create([
            'nama' => 'Admin Travelink',
            'email' => 'admin@travelink.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        User::factory()->create([
            'nama' => 'Penumpang Biasa',
            'email' => 'penumpang@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pembeli',
        ]);

        // Routes
        $route1 = Route::create([
            'kota_asal' => 'Bandung',
            'kota_tujuan' => 'Jakarta',
            'harga' => 150000,
            'estimasi_durasi_menit' => 180,
        ]);

        $route2 = Route::create([
            'kota_asal' => 'Bandung',
            'kota_tujuan' => 'Cianjur',
            'harga' => 80000,
            'estimasi_durasi_menit' => 90,
        ]);

        // Pickup Points
        $points1 = [
            ['nama_titik' => 'Pool Pasteur', 'alamat' => 'Jl. Dr. Djunjunan', 'tipe' => 'jemput'],
            ['nama_titik' => 'Cihampelas', 'alamat' => 'Jl. Cihampelas Bawah', 'tipe' => 'jemput'],
            ['nama_titik' => 'Terminal Lebak Bulus', 'alamat' => 'Kebayoran Lama', 'tipe' => 'turun'],
            ['nama_titik' => 'Slipi Jaya', 'alamat' => 'Slipi', 'tipe' => 'turun'],
        ];
        foreach ($points1 as $pt) {
            $route1->pickupPoints()->create($pt);
        }

        $points2 = [
            ['nama_titik' => 'Dipatiukur', 'alamat' => 'Jl. Dipatiukur', 'tipe' => 'jemput'],
            ['nama_titik' => 'Cimahi', 'alamat' => 'Jl. Raya Cimahi', 'tipe' => 'jemput'],
            ['nama_titik' => 'Terminal Pasir Hayam', 'alamat' => 'Jebrod', 'tipe' => 'turun'],
        ];
        foreach ($points2 as $pt) {
            $route2->pickupPoints()->create($pt);
        }

        // Vehicles
        $vehicle1 = Vehicle::create(['jenis' => 'Hiace', 'plat_nomor' => 'D 1111 TAA', 'kapasitas_kursi' => 15]);
        $vehicle2 = Vehicle::create(['jenis' => 'Hiace', 'plat_nomor' => 'D 2222 TAA', 'kapasitas_kursi' => 15]);
        $vehicle3 = Vehicle::create(['jenis' => 'Minibus', 'plat_nomor' => 'D 3333 TAA', 'kapasitas_kursi' => 19]);

        // Schedules
        $today = Carbon::today();
        
        $schedulesData = [
            ['route_id' => $route1->id, 'vehicle_id' => $vehicle1->id, 'waktu_berangkat' => $today->copy()->addHours(8)],
            ['route_id' => $route1->id, 'vehicle_id' => $vehicle3->id, 'waktu_berangkat' => $today->copy()->addHours(13)],
            ['route_id' => $route1->id, 'vehicle_id' => $vehicle1->id, 'waktu_berangkat' => $today->copy()->addHours(18)],
            ['route_id' => $route2->id, 'vehicle_id' => $vehicle2->id, 'waktu_berangkat' => $today->copy()->addHours(9)],
            ['route_id' => $route2->id, 'vehicle_id' => $vehicle2->id, 'waktu_berangkat' => $today->copy()->addHours(15)],
        ];

        foreach ($schedulesData as $sd) {
            $schedule = Schedule::create($sd);
            $capacity = $schedule->vehicle->kapasitas_kursi;

            for ($i = 1; $i <= $capacity; $i++) {
                $seatName = 'A' . $i;
                $schedule->seats()->create([
                    'nomor_kursi' => $seatName,
                    'status' => 'available'
                ]);
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UnlockExpiredSeats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seats:unlock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock seats that have exceeded their lock time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unlocked = \App\Models\Seat::where('status', 'locked')
            ->whereNotNull('locked_until')
            ->where('locked_until', '<', now())
            ->update([
                'status' => 'available',
                'locked_until' => null
            ]);
            
        $this->info("Unlocked {$unlocked} seats.");
    }
}

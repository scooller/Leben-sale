<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PlantReservationService;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservations:expire';

    protected $description = 'Expire plant reservations that have exceeded their time limit';

    public function handle(PlantReservationService $service): int
    {
        $count = $service->expireStaleReservations();

        if ($count > 0) {
            $this->info("Expired {$count} reservation(s).");
        }

        return self::SUCCESS;
    }
}

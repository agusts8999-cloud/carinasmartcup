<?php

namespace App\Console\Commands;

use App\Actions\ReleaseExpiredReservations;
use Illuminate\Console\Command;

class ReleaseExpiredOrdersCommand extends Command
{
    protected $signature = 'carina:release-expired-orders';

    protected $description = 'Release inventory reservations for expired pending-payment orders';

    public function handle(ReleaseExpiredReservations $action): int
    {
        $count = $action->handle();

        $this->info("Released {$count} expired order reservation(s).");

        return self::SUCCESS;
    }
}

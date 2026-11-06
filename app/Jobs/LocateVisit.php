<?php

namespace App\Jobs;

use App\Models\Visit;
use App\Support\Leads\IpLocation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Looks up the approximate location and network of a new visit's IP address
 * and copies it to the visitor (city, country, ISP shown in the admin panel).
 */
class LocateVisit implements ShouldQueue
{
    use Queueable;

    public function __construct(public Visit $visit) {}

    public function handle(IpLocation $ipLocation): void
    {
        $location = $ipLocation->lookup($this->visit->ip);

        $this->visit->update(['location' => $location]);

        if ($location['status'] !== 'ok') {
            return;
        }

        $this->visit->visitor->update([
            'country' => $location['country'] ?? null,
            'country_code' => $location['country_code'] ?? null,
            'region' => $location['region'] ?? null,
            'city' => $location['city'] ?? null,
            'network' => $location['network'] ?? null,
        ]);
    }
}

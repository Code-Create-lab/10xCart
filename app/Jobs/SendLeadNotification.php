<?php

namespace App\Jobs;

use App\Mail\NewLeadMail;
use App\Support\Leads\IpLocation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

/**
 * Emails a contact-form enquiry to the admins (recipients: config/site.php → lead_mail).
 */
class SendLeadNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** Seconds to wait before each retry (e.g. SMTP temporarily down). */
    public array $backoff = [60, 300];

    public function __construct(public array $lead) {}

    public function handle(IpLocation $ipLocation): void
    {
        $lead = $this->lead + ['location' => $ipLocation->lookup($this->lead['ip'] ?? null)];

        Mail::to(config('site.lead_mail.to'))
            ->cc(config('site.lead_mail.cc'))
            ->send(new NewLeadMail($lead));
    }
}

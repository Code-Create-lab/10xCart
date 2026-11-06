<?php

namespace App\Models;

use App\Support\Leads\LeadPresenter;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A contact-form enquiry. "context" is the cleaned visitor context (App\Support\Leads\VisitorContext),
 * "location" the IP lookup filled in by App\Jobs\SendLeadNotification.
 */
#[Fillable(['ref', 'visitor_id', 'name', 'phone', 'email', 'interest', 'plan', 'message', 'ip', 'context', 'location'])]
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'location' => 'array',
        ];
    }

    /** @return BelongsTo<Visitor, $this> */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /** Same rows as the lead email */
    public function presenter(): LeadPresenter
    {
        return new LeadPresenter([
            'ref' => $this->ref,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'interest' => $this->interest,
            'plan' => $this->plan,
            'message' => $this->message,
            'ip' => $this->ip,
            'submitted_at' => $this->created_at->toIso8601String(),
            'visitor' => $this->context ?? [],
            'location' => $this->location ?? ['status' => 'unavailable'],
        ]);
    }
}

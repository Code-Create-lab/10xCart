<?php

namespace App\Models;

use Database\Factories\VisitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One session of a visitor: where it came from, how long they stayed, how far they scrolled.
 */
#[Fillable([
    'key', 'started_at', 'last_activity_at', 'ip', 'location', 'source', 'referrer', 'landing',
    'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'click_id',
])]
class Visit extends Model
{
    /** @use HasFactory<VisitFactory> */
    use HasFactory;

    /** Same as the column defaults, so counters work on a model that was just created */
    protected $attributes = [
        'page_views' => 0,
        'engaged_seconds' => 0,
        'max_scroll' => 0,
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'location' => 'array',
        ];
    }

    /** @return BelongsTo<Visitor, $this> */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /** @return HasMany<VisitorEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(VisitorEvent::class);
    }
}

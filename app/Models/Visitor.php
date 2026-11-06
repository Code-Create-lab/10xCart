<?php

namespace App\Models;

use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A browser that accepted analytics cookies, identified by the "10x_vid" cookie.
 * Deleted automatically (with its visits and events) after config('site.tracking.retention_days')
 * without activity: `php artisan model:prune` runs daily (routes/console.php).
 */
#[Fillable([
    'uuid', 'first_seen_at', 'last_seen_at', 'first_source', 'last_source', 'last_ip',
    'country', 'country_code', 'region', 'city', 'network',
    'device', 'os', 'browser', 'model', 'screen', 'language', 'timezone', 'traits', 'user_agent',
])]
class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use HasFactory, Prunable;

    /** Same as the column defaults, so counters work on a model that was just created */
    protected $attributes = [
        'visits_count' => 0,
        'page_views_count' => 0,
        'engaged_seconds' => 0,
    ];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'traits' => 'array',
        ];
    }

    /** @return HasMany<Visit, $this> */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /** @return HasMany<Lead, $this> */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /** "Nagpur, Maharashtra, India" */
    public function place(): ?string
    {
        $parts = array_unique(array_filter([$this->city, $this->region, $this->country]));

        return $parts ? implode(', ', $parts) : null;
    }

    public function prunable(): Builder
    {
        return static::where('last_seen_at', '<', now()->subDays(config('site.tracking.retention_days')));
    }
}

<?php

namespace App\Models;

use Database\Factories\VisitorEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Something a visitor did during a visit (see TYPES).
 */
#[Fillable(['type', 'name', 'path', 'created_at'])]
class VisitorEvent extends Model
{
    /** @use HasFactory<VisitorEventFactory> */
    use HasFactory;

    /** Event types the tracker may send => label shown in the admin panel */
    public const TYPES = [
        'page_view' => 'Opened page',
        'section' => 'Scrolled to',
        'click' => 'Clicked',
        'form_start' => 'Started the contact form',
        'form_submit' => 'Sent the contact form',
    ];

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Visit, $this> */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function label(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}

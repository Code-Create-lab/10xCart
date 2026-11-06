<?php

namespace Database\Factories;

use App\Models\Visit;
use App\Models\VisitorEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitorEvent>
 */
class VisitorEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'type' => 'section',
            'name' => 'Pricing',
            'path' => '/',
            'created_at' => now(),
        ];
    }
}

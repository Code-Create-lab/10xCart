<?php

namespace Database\Factories;

use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'visitor_id' => Visitor::factory(),
            'key' => Str::random(12),
            'started_at' => now()->subMinutes(10),
            'last_activity_at' => now()->subMinutes(5),
            'ip' => fake()->ipv4(),
            'source' => 'Google search',
            'landing' => '/',
        ];
    }
}

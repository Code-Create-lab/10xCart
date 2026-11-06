<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstSeen = fake()->dateTimeBetween('-30 days', '-1 hour');

        return [
            'uuid' => fake()->uuid(),
            'first_seen_at' => $firstSeen,
            'last_seen_at' => $firstSeen,
            'first_source' => 'Google search',
            'last_source' => 'Google search',
            'last_ip' => fake()->ipv4(),
            'country' => 'India',
            'country_code' => 'IN',
            'region' => 'Maharashtra',
            'city' => fake()->randomElement(['Nagpur', 'Pune', 'Mumbai']),
            'device' => 'Mobile',
            'os' => 'Android 14',
            'browser' => 'Chrome 129',
            'language' => 'en-IN',
            'timezone' => 'Asia/Kolkata',
        ];
    }
}

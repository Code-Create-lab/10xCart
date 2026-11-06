<?php

namespace Database\Factories;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ref' => 'L'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
            'name' => fake()->name(),
            'phone' => '9'.fake()->numerify('#########'),
            'email' => fake()->safeEmail(),
            'interest' => fake()->randomElement(StoreLeadRequest::INTERESTS),
            'plan' => fake()->randomElement(StoreLeadRequest::PLANS),
            'message' => fake()->sentence(),
            'ip' => fake()->ipv4(),
            'context' => [],
        ];
    }
}

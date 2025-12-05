<?php

namespace Database\Factories;

use App\Models\ScrapedFine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScrapedFine>
 */
class ScrapedFineFactory extends Factory
{
    protected $model = ScrapedFine::class;

    public function definition(): array
    {
        return [
            'organisation' => $this->faker->company(),
            'regulator' => $this->faker->word(),
            'sector' => $this->faker->word(),
            'region' => $this->faker->country(),
            'fine_amount' => $this->faker->numberBetween(100000, 5000000),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP', 'AUD', 'CAD']),
            'fine_date' => $this->faker->date(),
            'law' => $this->faker->word(),
            'articles_breached' => 'Art. ' . $this->faker->numberBetween(1, 10),
            'violation_type' => $this->faker->randomElement(['Data Breach', 'Privacy Violation', 'Compliance Failure']),
            'summary' => $this->faker->sentence(15),
            'badges' => 'gdpr,privacy',
            'link_to_case' => $this->faker->url(),
            'status' => 'pending',
            'submitted_by' => User::factory()->create(['is_admin' => true])->id,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_notes' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'reviewed_by' => User::factory()->create(['is_admin' => true])->id,
                'reviewed_at' => now(),
                'review_notes' => 'Approved - information verified.',
            ];
        });
    }

    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'reviewed_by' => User::factory()->create(['is_admin' => true])->id,
                'reviewed_at' => now(),
                'review_notes' => 'Rejected - insufficient documentation.',
            ];
        });
    }
}

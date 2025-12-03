<?php

namespace Database\Factories;

use App\Models\GlobalFine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GlobalFine>
 */
class GlobalFineFactory extends Factory
{
    protected $model = GlobalFine::class;

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
        ];
    }
}

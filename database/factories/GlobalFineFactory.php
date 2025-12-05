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
            'regulator' => $this->faker->randomElement([
                'ICO (UK)','CNIL (France)','BfDI (Germany)','DPC (Ireland)','AEPD (Spain)',
                'FTC (USA)','OAIC (Australia)','OPC (Canada)','CNPD (Luxembourg)'
            ]),
            'sector' => $this->faker->randomElement([
                'Finance & Banking','Healthcare','Technology','Retail & E-commerce','Telecommunications',
                'Public Sector','Education','Aviation / Transportation','Social Media'
            ]),
            'region' => $this->faker->randomElement(['EU / EEA','USA','Australia','Canada','Global']),
            'fine_amount' => $this->faker->numberBetween(100000, 5000000),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP', 'AUD', 'CAD']),
            'fine_date' => $this->faker->date(),
            'law' => $this->faker->randomElement(['GDPR','UK GDPR','DPA 2018','CCPA','Other']),
            'articles_breached' => 'Art. ' . $this->faker->numberBetween(1, 10),
            'violation_type' => $this->faker->randomElement([
                'Security Breach','Inadequate Security','Consent Issues','Transparency',
                'Data Transfer','Unlawful Processing','Childrens Privacy'
            ]),
            'summary' => $this->faker->sentence(15),
            'badges' => 'gdpr,privacy',
            'link_to_case' => $this->faker->url(),
        ];
    }
}

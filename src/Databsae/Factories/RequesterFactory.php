<?php

namespace mmerlijn\LaravelSalt\Databsae\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\LaravelSalt\Models\Requester;

class RequesterFactory extends Factory
{
    protected $model = Requester::class;

    public function definition(): array
    {
        return [
            'agbcode' => (string) $this->faker->unique()->numberBetween(10000000, 99999999),
            'type' => 'zorgverlener',
            'sex' => $this->faker->randomElement(['M', 'F', 'U']),
            'vektis_name' => $this->faker->company(),
            'initials' => strtoupper($this->faker->lexify('??')),
            'lastname' => $this->faker->lastName(),
            'own_lastname' => $this->faker->lastName(),
            'prefix' => null,
            'own_prefix' => $this->faker->optional()->randomElement(['van', 'de', 'van de']),
            'postcode' => strtoupper(str_replace(' ', '', $this->faker->postcode())),
            'city' => $this->faker->city(),
            'phone' => $this->faker->phoneNumber(),
            'street' => $this->faker->streetName(),
            'building' => $this->faker->buildingNumber(),
            'postbus' => null,
            'extra_address_line' => null,
            'email' => $this->faker->safeEmail(),
            'fax' => null,
            'mobile' => null,
            'is_gp' => $this->faker->randomElement(['Y', 'N']),
            'qualifications' => [],
            'owners' => [],
            'vektis_at' => now()->subDays($this->faker->numberBetween(0, 90)),
            'started_at' => now()->subYears($this->faker->numberBetween(0, 10)),
        ];
    }

    public function gp(): self
    {
        return $this->state(fn () => [
            'type' => 'zorgverlener',
            'is_gp' => 'Y',
        ]);
    }

    public function zorgverlener(): self
    {
        return $this->state(fn () => [
            'type' => 'zorgverlener',
        ]);
    }

    public function organization(): self
    {
        return $this->state(fn () => [
            'type' => 'onderneming',
            'is_gp' => 'N',
        ]);
    }
}




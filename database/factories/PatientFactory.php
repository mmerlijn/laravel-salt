<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\LaravelSalt\Models\Patient;
use mmerlijn\msgRepo\Enums\PatientSexEnum;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->optional(0.2)->safeEmail(),
            'sex' => $this->faker->randomElement([PatientSexEnum::FEMALE, PatientSexEnum::MALE]),
            'initials' => $this->faker->randomElement(['A', 'BV', 'C', 'DQ', 'E', 'F', 'GK', 'H', 'I', 'J']),
            'lastname' => $this->faker->optional(0.2)->lastName(),
            'own_lastname' => $this->faker->lastName(),
            'prefix' => null,
            'own_prefix' => $this->faker->optional(0.2)->randomElement(['van', 'van de', "op 't", 'de']),
            'dob' => \Carbon\Carbon::parse($this->faker->dateTimeThisYear())->subYears($this->faker->numberBetween(0, 102))->format('Y-m-d'),
            'bsn' => $this->faker->optional(0.95)->idNumber(),
            'postcode' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'building' => $this->faker->buildingNumber(),
            'last_requester' => $this->faker->randomElement(['01000017', '01000029', '01000039', '01000099', '01000184', '01000225']),
            'last_organization' => $this->faker->randomElement(['01011451']),
            'phone' => $this->faker->phoneNumber(),
            'phone2' => $this->faker->phoneNumber(),
            'uzovi' => $this->faker->randomElement([3332, 3333, 3334, 3336, 3343, 3344, 5515]),
            'policy_nr' => $this->faker->optional(0.7)->randomNumber(8, true),
            //'lbs_nr' => $this->faker->optional(0.5)->randomNumber(9, true),
        ];
    }

    public function setBsn(string $bsn): self
    {
        return $this->state(function (array $attributes) use ($bsn) {
            return [
                'bsn' => $bsn,
            ];
        });
    }
    public function setLabtrainId(string $id): self
    {
        return $this->state(function (array $attributes) use ($id) {
            return [
                'labtrain_id' => $id,
            ];
        });
    }

    public function setPostcode(string $postcode): self
    {
        return $this->state(fn(array $attributes) => [
            'postcode' => $postcode,
        ]);
    }
}

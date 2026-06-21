<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\Patient;

class FlowExchangeFactory extends Factory
{
    protected $model = FlowExchange::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->numberBetween(1, 10),
            'request' => 'Hallo wereld',
        ];
    }

    public function type(int $type): self
    {
        return $this->state(fn() => [
            'type' => $type,
        ]);
    }

    public function request(string $request): self
    {
        return $this->state(fn() => [
            'request' => $request,
        ]);
    }

    public function patient(Patient $patient): self
    {
        return $this->state(fn() => [
            'patient_id' => $patient->id,
        ]);
    }

    public function request_nr(string $request_nr): self
    {
        return $this->state(fn() => [
            'request_nr' => $request_nr,
        ]);
    }
}


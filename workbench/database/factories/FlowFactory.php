<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowError;

class FlowFactory extends Factory
{
    protected $model = Flow::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->numberBetween(1, 10),
            'stack' => [$this->faker->numberBetween(1, 10)],
            'attempts' => 0,
            'try_after' => now()->subMinute(),
            'flow_error_id' => null,
        ];
    }

    public function error(?FlowError $flowError = null): self
    {
        return $this->state(fn() => [
            'flow_error_id' => $flowError?->id ?? FlowError::factory(),
        ]);
    }

    public function payload(Model $payload): self
    {
        return $this->state(fn() => [
            'payload_type' => $payload->getMorphClass(),
            'payload_id' => $payload->id,
        ]);
    }
}


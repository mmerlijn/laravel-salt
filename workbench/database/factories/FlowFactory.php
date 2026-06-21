<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\Patient;

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
            'app_error_id' => null,
        ];
    }

    public function appError(?AppError $appError = null): self
    {
        return $this->state(fn () => [
            'app_error_id' => $appError?->id ?? AppError::factory(),
        ]);
    }

    public function payload(string $payloadType, int $payloadId): self
    {
        return $this->state(fn () => [
            'payload_type' => $payloadType,
            'payload_id' => $payloadId,
        ]);
    }
}


<?php

namespace mmerlijn\LaravelSalt\Databsae\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Models\AppError;

class AppErrorFactory extends Factory
{
    protected $model = AppError::class;

    public function definition(): array
    {
        $levels = array_map(static fn (ErrorLevelEnum $level) => $level->value, ErrorLevelEnum::cases());

        return [
            'level' => $this->faker->randomElement($levels),
            'from_type' => null,
            'from_id' => null,
            'at_type' => null,
            'at_id' => null,
            'class' => $this->faker->optional()->word(),
            'solution' => $this->faker->optional()->sentence(),
            'message' => $this->faker->sentence(),
            'trace' => $this->faker->optional()->text(400),
            'exception_class' => $this->faker->optional()->randomElement([
                null,
                \RuntimeException::class,
                \Exception::class,
            ]),
            'notify' => $this->faker->boolean(20),
            'notified' => [],
        ];
    }
}


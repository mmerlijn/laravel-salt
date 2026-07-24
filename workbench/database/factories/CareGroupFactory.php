<?php

namespace Workbench\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\LaravelSalt\Enums\CareGroupEnum;
use mmerlijn\LaravelSalt\Enums\TestTypeEnum;


class CareGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'test_type' => TestTypeEnum::FUNDUS,
            'agbcode' => $this->faker->randomElement(['01004014', '01020092', '01020152', '01020826', '01021560', '01024756', '01025355', '01026319', '03069109', '03314948', '04100203', '08000473']),
            'care_group' => $this->faker->randomElement(CareGroupEnum::values()),
            'requester_type' => $this->faker->randomElement(['gp', 'requester']),
            'requester_id' => 1,
        ];
    }
}

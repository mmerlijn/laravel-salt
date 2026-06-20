<?php

namespace mmerlijn\LaravelSalt\Databsae\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use mmerlijn\LaravelSalt\Enums\NoteSubjectEnum;
use mmerlijn\LaravelSalt\Enums\NoteTypeEnum;
use mmerlijn\LaravelSalt\Models\Note;
use mmerlijn\LaravelSalt\Models\Patient;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        return [
            'subject_type' => NoteSubjectEnum::patient->value,
            'subject_id' => Patient::factory(),
            'type' => NoteTypeEnum::NOTE->value,
            'note' => $this->faker->sentence(),
            'created_by' => 500,
            'delete_after' => now()->addDays(30),
        ];
    }

    public function forPatient(?Patient $patient = null): self
    {
        return $this->state(fn () => [
            'subject_type' => NoteSubjectEnum::patient->value,
            'subject_id' => $patient?->id ?? Patient::factory(),
        ]);
    }
}


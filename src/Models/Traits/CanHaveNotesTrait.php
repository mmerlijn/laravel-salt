<?php

namespace mmerlijn\LaravelSalt\Models\Traits;

use App\Models\Note;

trait CanHaveNotesTrait
{
    public function notes()
    {
        return $this->morphMany(Note::class, 'subject');
    }
}

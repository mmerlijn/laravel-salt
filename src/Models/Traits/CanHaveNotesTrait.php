<?php

namespace mmerlijn\LaravelSalt\Models\Traits;



use mmerlijn\LaravelSalt\Models\Note;

trait CanHaveNotesTrait
{
    public function notes()
    {
        return $this->morphMany(Note::class, 'subject');
    }
}

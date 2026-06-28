<?php

namespace mmerlijn\LaravelSalt\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use mmerlijn\LaravelSalt\Models\Note;

/** @mixin Note */
class NoteResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'label' => $this->type->label(),
            'note' => $this->note,
            'created_at' => $this->created_at->diffForHumans(),
            'creator' => UserResource::make($this->creator),
            'date' => $this->created_at->format("j/n"),
        ];
    }
}

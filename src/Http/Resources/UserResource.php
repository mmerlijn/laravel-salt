<?php

namespace mmerlijn\LaravelSalt\Http\Resources;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'two_factor_enabled' => $this->two_factor_confirmed_at ? true : false,
            'email' => $this->email,
            'profile_photo_url' => $this->profile_photo_path,
            'can' => $this->getAllPermissions()->map(fn($i) => $i->name)->toArray(),
            'avatar' => $this->avatar,
        ];
    }
}

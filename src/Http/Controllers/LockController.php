<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Http\Resources\UserResource;
use mmerlijn\LaravelSalt\Models\Lock;

class LockController extends Controller
{
    public function lock(string $type, int $id)
    {
        $lock = Lock::updateOrCreate([
            'locked_type' => $type,
            'locked_id' => $id,
        ], [
            'lock_end' => now()->addSeconds(90),
            'user_id' => auth()->user()?->id ?: 500,
        ]);
        return response()->json($lock->user_id ? $lock->user->toResource() : null);
    }

    public function locked(string $type, int $id)
    {
        $lock = Lock::whereLockedType($type)->whereLockedId($id)->first();
        if ($lock) {
            if ($lock->user_id != auth()->user()?->id) {
                return response()->json(['lock' => true, 'user' => UserResource::make($lock->user)]);
            }
        }
        return response()->json(['lock' => false, 'user' => null]);

    }
}

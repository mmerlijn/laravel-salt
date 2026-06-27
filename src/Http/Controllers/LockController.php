<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Http\Resources\UserResource;
use mmerlijn\LaravelSalt\Models\Lock;

class LockController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'id' => 'required',
        ]);

        if (!$lock = Lock::find($request->id)) {
            $lock = Lock::updateOrCreate([
                'locked_type' => $request->type,
                'locked_id' => $request->id,
            ], [
                'lock_end' => now()->addSeconds(90),
                'user_id' => auth()->user()?->id ?: 500,
            ]);
        } else {
            $lock->extend();
        }
        return response()->json($lock->user_id ? $lock->user->toResource() : null);
    }

    public function show(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'id' => 'required',
        ]);
        $lock = Lock::whereLockedType($request->type)->whereLockedId($request->id)->first();
        if ($lock) {
            if ($lock->user_id != auth()->user()?->id) {
                return response()->json(['lock' => true, 'user' => UserResource::make($lock->user)]);
            }
        }
        return response()->json(['lock' => false, 'user' => null]);

    }
}

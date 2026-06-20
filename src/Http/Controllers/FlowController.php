<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use mmerlijn\LaravelSalt\Http\Resources\FlowResource;
use mmerlijn\LaravelSalt\Models\Flow;

class FlowController
{
    public function index(Request $request)
    {
        $query = Flow::query()->with('error');

        if ($request->filled('app_error_id')) {
            $query->where('app_error_id', $request->integer('app_error_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->integer('type'));
        }

        return FlowResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15))
        );
    }

    public function show(Request $request, Flow $flow): JsonResponse
    {
        return response()->json([
            'data' => FlowResource::make($flow->load('error'))->resolve(),
        ]);
    }

    public function edit(Request $request, Flow $flow): JsonResponse
    {
        return response()->json([
            'data' => FlowResource::make($flow->load('error'))->resolve(),
        ]);
    }

    public function update(Request $request, Flow $flow): JsonResponse
    {
        $flow->forceFill([
            'try_after' => now(),
        ])->save();

        return response()->json([
            'data' => FlowResource::make($flow->load('error'))->resolve(),
        ]);
    }

    public function destroy(Flow $flow): Response
    {
        $flow->delete();

        return response()->noContent();
    }
}

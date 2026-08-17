<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Http\Resources\FlowResource;
use mmerlijn\LaravelSalt\Models\Flow;

class FlowController extends Controller
{
    public function index(Request $request)
    {
        $query = Flow::query()->with('error');

        if ($request->filled('flow_error_id')) {
            $query->where('flow_error_id', $request->integer('flow_error_id'));
        }

        if ($request->filled('type')) {
            $query->where('from_type', $request->type);
        }
        if ($request->filled('type_id')) {
            $query->where('from_id', $request->integer('type_id'));
        }
        if ($request->filled('id')) {
            $query->where('id', $request->integer('id'));
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
    public function showByType(Request $request): JsonResponse
    {
        return response()->json([
            'data' => FlowResource::make(
                Flow::whereFromType($request->from_type)
                    ->whereFromId($request->from_id)
                    ->load('error')->first()
            )->resolve(),
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

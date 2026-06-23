<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use mmerlijn\LaravelSalt\Http\Resources\FlowErrorResource;
use mmerlijn\LaravelSalt\Models\FlowError;

class FlowErrorController
{
    public function index(Request $request)
    {
        $query = FlowError::query()->with('flows');

        if ($request->filled('level')) {
            $query->level((int) $request->integer('level'));
        }

        if ($request->filled('class')) {
            $query->forClass((string) $request->string('class'));
        }

        if ($request->filled('notify')) {
            $query->where('notify', filter_var($request->input('notify'), FILTER_VALIDATE_BOOL));
        }

        if ($request->boolean('with_exception_class')) {
            $query->withExceptionClass();
        }

        return FlowErrorResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15))
        );
    }
    public function show(Request $request, FlowError $FlowError): JsonResponse
    {
        return response()->json([
            'data' => $FlowError->toResource(),
        ]);
    }
    public function edit(Request $request, FlowError $FlowError): JsonResponse
    {
        return response()->json([
            'data' => $FlowError->toResource(),
        ]);
    }
    public function update(Request $request, FlowError $FlowError): JsonResponse
    {
        $data = $request->validate([
            'level' => ['sometimes', 'integer', 'min:1'],
            'solution' => ['sometimes', 'nullable', 'string'],
            'message' => ['sometimes', 'nullable', 'string'],
            'trace' => ['sometimes', 'nullable', 'string'],
            'exception_class' => ['sometimes', 'nullable', 'string'],
            'notify' => ['sometimes', 'boolean'],
            'notified' => ['sometimes', 'array'],
            'class' => ['sometimes', 'nullable', 'string'],
        ]);

        $data['notify'] = array_key_exists('notify', $data)
            ? (bool) $data['notify']
            : (bool) $FlowError->notify;

        $data['notified'] = $data['notified'] ?? ($FlowError->notified ?? []);

        $FlowError->update($data);

        $FlowError->refresh();

        return response()->json([
            'data' => $FlowError->toResource(),
        ]);
    }

    public function destroy(FlowError $FlowError): Response
    {
        $FlowError->delete();
        return response()->noContent();
    }


}

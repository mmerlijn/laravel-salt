<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Http\Resources\FlowErrorResource;
use mmerlijn\LaravelSalt\Models\FlowError;

class FlowErrorController extends Controller
{
    public function index(Request $request)
    {
        $query = FlowError::query()->with('flow');

        if ($request->filled('level') and $request->leve > 0) {
            $query = $query->whereLevel($request->level);
        }
        if ($request->filled('code') and $request->code > 0) {
            $query = $query->whereCode($request->code);
        }

        if ($request->filled('message')) {
            $query = $query->where('message', 'like', '%' . $request->message . '%');
        }
        if ($request->filled('class')) {
            $query = $query->where('class', 'like', '%' . $request->string('class') . '%');
        }

        if ($request->filled('notify')) {
            $query = $query->where('notify', filter_var($request->input('notify'), FILTER_VALIDATE_BOOL));
        }


        return FlowErrorResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15))
        );
    }

    public function show(Request $request, FlowError $flowError): JsonResponse
    {
        return response()->json([
            'data' => $flowError->toResource(),
        ]);
    }

    public function edit(Request $request, FlowError $flowError): JsonResponse
    {
        return response()->json([
            'data' => $flowError->toResource(),
        ]);
    }

    public function update(Request $request, FlowError $flowError): JsonResponse
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
            ? (bool)$data['notify']
            : (bool)$flowError->notify;

        $data['notified'] = $data['notified'] ?? ($flowError->notified ?? []);

        $flowError->update($data);

        $flowError->refresh();

        return response()->json([
            'data' => $flowError->toResource(),
        ]);
    }

    public function destroy(FlowError $flowError): Response
    {
        $flowError->delete();
        return response()->noContent();
    }


}

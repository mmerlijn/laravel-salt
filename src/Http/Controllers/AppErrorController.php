<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use mmerlijn\LaravelSalt\Http\Resources\AppErrorResource;
use mmerlijn\LaravelSalt\Models\AppError;

class AppErrorController
{
    public function index(Request $request)
    {
        $query = AppError::query()->with('flows');

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

        return AppErrorResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15))
        );
    }
    public function show(Request $request, AppError $appError): JsonResponse
    {
        return response()->json([
            'data' => $appError->toResource(),
        ]);
    }
    public function edit(Request $request, AppError $appError): JsonResponse
    {
        return response()->json([
            'data' => $appError->toResource(),
        ]);
    }
    public function update(Request $request, AppError $appError): JsonResponse
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
            : (bool) $appError->notify;

        $data['notified'] = $data['notified'] ?? ($appError->notified ?? []);

        $appError->update($data);

        $appError->refresh();

        return response()->json([
            'data' => $appError->toResource(),
        ]);
    }

    public function destroy(AppError $appError): Response
    {
        $appError->delete();
        return response()->noContent();
    }


}

<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use mmerlijn\LaravelSalt\Enums\CareGroupEnum;
use mmerlijn\LaravelSalt\Enums\TestTypeEnum;
use mmerlijn\LaravelSalt\Models\CareGroup;


class CareGroupApiController
{
    public function index(Request $request)
    {
        $careGroups = CareGroup::filter($request->toArray())->with(['requester'])
            ->orderBy('test_type')
            ->orderBy('care_group');
        return response()->json(
            $careGroups->simplePaginate($request->integer('per_page', 30))
                ->toResourceCollection()
        );
    }

    public function attachRequester(Request $request)
    {
        $request->validate([
            'care_group' => ['required', Rule::enum(CareGroupEnum::class)],
            'agbcode' => ['required', 'exists:requesters,agbcode'],
            'test_type' => ['required', Rule::enum(TestTypeEnum::class)],
        ]);
        $careGroup = CareGroup::firstOrNew(
            [
                'agbcode' => $request->input('agbcode'),
                'care_group' => $request->input('care_group'),
                'test_type' => $request->input('test_type'),
            ]);
        return response()->json(['message' => 'Requester attached successfully']);
    }

    public function detachRequester(Request $request)
    {
        $request->validate([
            'care_group' => ['required', Rule::enum(CareGroupEnum::class)],
            'agbcode' => ['required', 'exists:requesters,agbcode'],
            'test_type' => ['required', Rule::enum(TestTypeEnum::class)],
        ]);
        CareGroup::where('agbcode', $request->input('agbcode'))
            ->where('care_group', $request->input('care_group'))
            ->where('test_type', $request->input('test_type'))
            ->delete();
        return response()->json(['message' => 'Requester detached successfully']);
    }
}

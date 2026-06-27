<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Http\Resources\Requester\RequesterNestedResource;
use mmerlijn\LaravelSalt\Models\Requester;

class RequesterApiController extends Controller
{
    public function index(Request $request)
    {

        return response()->json(Requester::filter($request->toArray())->with(['members', 'organizations'])->limit($request->limit ?? 8)->get()->toResourceCollection(RequesterNestedResource::class));
    }

    public function show(Requester $requester)
    {
        return response()->json($requester->toResource());
    }
}

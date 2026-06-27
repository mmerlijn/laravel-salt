<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Models\Uzovi;

class UzoviApiController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Uzovi::name($request->q)
                ->active(!!$request->active)
                ->limit($request->limit ?? 8)
                ->get()
                ->toResourceCollection());
    }

    public function show(Uzovi $uzovi)
    {
        return response()->json($uzovi->toResource());
    }
}

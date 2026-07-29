<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Helpers\VektisGrabber;
use mmerlijn\LaravelSalt\Http\Resources\Requester\RequesterNestedResource;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Enums\VektisType;

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

    public function storeVektisAgbcode(HttpRequest $request)
    {
        $request->validate(['agbcode' => 'required|regex:/^\d{8}$/']);

        new VektisGrabber()(VektisType::ZORGVERLENER, $request->agbcode);

        $requester = Requester::whereAgbcode($request->agbcode)->first();
        if ($requester) {
            return response()->json($requester->toResource());
        } else {
            return response()->json(['error' => 'Aanvrager niet gevonden'], 404);
        }
    }
}

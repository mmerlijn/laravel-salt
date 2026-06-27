<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Models\Patient;

class PatientApiController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Patient::filtered($request->toArray())->get()->toResourceCollection());
    }
}

<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Models\Patient;

class PatientApiController extends Controller
{
    public function index(Request $request)
    {
        $p = Patient::query();
        if ($request->bsn) {
            $p = $p->useIndex('patient_index');
        } else {
            $p = $p->useIndex('patient_search_index');
        }
        return response()->json($p->filtered($request->toArray())
            ->simplePaginate(10)->withQueryString()->toResourceCollection());
    }
}

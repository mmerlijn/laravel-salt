<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use mmerlijn\LaravelSalt\Models\Patient;

class PatientApiController extends Controller
{
    public function index(Request $request)
    {
        $p = Patient::filtered($request->toArray());
        if ($request->bsn) {
            $p = $p->useIndex('patient_index')
                ->limit(1)->get();
        } else {
            $p = $p->useIndex('patient_search_index')
                ->simplePaginate(10)->withQueryString();
        }
        return response()->json($p->toResourceCollection());
    }
}

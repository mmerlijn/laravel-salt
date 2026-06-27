<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EnumApiController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $enum)
    {
        try {
            $enum = "App\\Enums\\" . $enum;
            return $enum::collection();
        } catch (\Throwable $th) {
            return [];
        }
    }

}

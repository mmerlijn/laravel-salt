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

            $enumClass = "App\\Enums\\" . $enum;

            if (!class_exists($enumClass)) {
                $enumClass = "\\mmerlijn\\LaravelSalt\\Enums\\" . $enum;
                if (!class_exists($enumClass)) {
                    throw new \Exception("Enum {$enumClass} not found");
                }
            }
            return $enumClass::collection();
        } catch (\Throwable $th) {
            return [];
        }
    }

}

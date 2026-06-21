<?php

use mmerlijn\LaravelSalt\Actions\Tasks\GetRequestNrFromHelplineJson;

it('Can read request_nr from JSON file', function (string $json, ?string $expected) {
    $request_nr = (new GetRequestNrFromHelplineJson)($json);
    expect($request_nr)->toBe($expected);
})->with([
    [
        '{
    "EmployeeInitials": "S.",
    "ClientDateOfBirth": 20010809,
    "orderPlacer": "Zorgdomein",
    "ProductCode": "KCLVN",
    "orderNumber": "REQ1234567890",
    "MessageId": "E96D0E51-09EB-4297-85FD-0A823AA58E76",
    "EmployeeNumber": "000078",
    "Action": 3,
    "Forms": []
    }', 'REQ1234567890'
    ], [
        '{
    "EmployeeInitials": "S.",
    "ClientDateOfBirth": 20010809,
    "Action": 3,
    "Forms": [
        {
            "Fields": {
                "tubesSALT": null,
                "orderNumber": "REQ1234567890",
                "productcodes": "KCLVN",
                "addresssecondline": "1054MP AMSTERDAM"
                }
        }
    ]
    }', 'REQ1234567890'
    ],
    [
        '{}', null
    ]
]);

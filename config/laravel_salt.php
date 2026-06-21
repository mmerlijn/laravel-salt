<?php


return [
    'tasks' => [
        //example
        100 => \mmerlijn\LaravelSalt\Jobs\Tasks\GetRequestNrFromHl7Job::class,
        101 => \mmerlijn\LaravelSalt\Jobs\Tasks\GetRequestNrFromHelplineJsonJob::class,
        102 => \mmerlijn\LaravelSalt\Jobs\Tasks\GetPatientIdFromHl7Job::class,
        110 => \mmerlijn\LaravelSalt\Jobs\Tasks\Task110PingForResponseJob::class,
        // 103 => GetLabtrainPatientNrFromJsonJob::class,
        8000 => \mmerlijn\LaravelSalt\Jobs\Tasks\Send8000responsesJob::class,
    ],
    'flows' => [

        //example format patient


        250 => [],
        255 => [],
        8232 => [8000],
        8223 => [8000],
        8221 => [8000],
        8235 => [8000],
        8236 => [8000],
        8230 => [8000],
        8250 => [8000],
        8255 => [8000],
        //8000 en hoger is een send response/request zonder verdere stack
    ],
    'notify' => [ //errorLevel => [] //user_ids /email-adresses
        1 => [1], //user 1
        2 => ['piet@test.me', 'klaas@test.me'],
        3 => [2, 3, 'henk@test.me'], //users 2 and 3, and henk
    ],
    'mirth_server' => "",
    "mirth_port" => 8200,
    'mirth_ports' => [
        8232 => [8232, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // edifact to huisarts niet thuis bij prikafspraak
        8223 => [8223, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // OLVG out (type moet nog juist gezet worden)
        8221 => [8221, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // salt_by_testcode_out (type moet nog juist gezet worden)
        8235 => [8235, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // helpline output helpline result
        8236 => [8236, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // helpline OLVG output helpline result
        8230 => [8230, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // helpline input (request) naar helpline
        8250 => [8250, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // patient_nr ophalen ->update patient lbs_nr
        8255 => [8255, \mmerlijn\LaravelSalt\Enums\SendTypeEnum::MIRTH_TCP], // Patientnr before aanvraag opsturen
    ],
    'api_uri' => [
        //int => url
    ],
    'application' => '', //agenda or saltnet
    'vektis' => false,
    'classes' => [
        'appointment' => '',
        'appointmentCreation' => '',
        'mijnsaltContact' => '',
        'followup' => '',
        'test' => '',
        'request' => '',
        'getCaregiver' => '',
    ]
];
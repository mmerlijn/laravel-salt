<?php


return [
    'tasks' => [
        //example
        101 => \Workbench\App\Jobs\FlowExampleTask1Job::class,
        102 => \Workbench\App\Jobs\FlowExampleTask2Job::class,
        103 => \Workbench\App\Jobs\FlowExampleTask3Job::class,
        104 => \Workbench\App\Jobs\FlowExampleTask3Job::class,
    ],
    'flows'=>[
        //example format patient
        10 => [101,103], // patient->labtrain_id 54321
        20 => [102], //app, error
        30 => [[101,103],104], // patient->labtrain_id= 890
    ],
    'notify' => [ //errorLevel => [] //user_ids /email-adresses
        1 => [ 1], //user 1
        2 => ['piet@test.me','klaas@test.me'],
        3 => [2,3,'henk@test.me'], //users 2 and 3, and henk
    ],
    'application'=> '', //agenda or saltnet
    'vektis'=>false,
    'classes' =>[
        'appointment' => '',
        'appointmentCreation' => '',
        'mijnsaltContact' => '',
        'followup' => '',
        'test' => '',
        'request' => '',
        'getCaregiver' => '',
    ]
];
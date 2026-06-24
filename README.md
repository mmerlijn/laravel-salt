# Laravel Salt

Laravel Salt bundles shared SALT utilities for Laravel projects.

## Installatie

Installeer het package in je Laravel project met Composer:

```bash
composer require mmerlijn/laravel-salt
```

## Publish package views

Use this command to publish all views from this package to your application so you can customize them:

```bash
php artisan vendor:publish --provider="mmerlijn\LaravelSalt\LaravelSaltServiceProvider" --tag=views
```

Published files will be placed in:

- `resources/views/vendor/laravel-salt`

### Taken die periodiek uitgevoerd worden

```php
(PruneLocks)->everyMinute();
(FlowRunnerJob)->everyMinute();

```

## Flows

### Starten van een flow

Een flow kan aangemaakt worden met

```php
Flow::add(flow: <int|Enum>, payload: <Class|array|null>, wait: <int (minuten)>, data: <array>);
```

### Flow type

De flow type bepaalt welke stack van taken uitgevoerd wordt. De flow type is een integer of Enum die in het configratie
bestand is gedefinieerd.

```php
    'flows'=>[
        10 => [101,103], //uitvoeren van taken 101, 103 (in deze volgorde)
        20 => [102],  //uitvoeren van taak 102
        30 => [[101,102],103], //uitvoeren van taken 101, 102, 103  waarbij 101 en 102 eerst (tegelijkertijd) worden uitgevoerd (in deze volgorde)
```

### Stack van een flow

Een flow bestaat uit een aantal Tasks (die in het configratie bestand zijn gedefinieerd). Deze taken staan in de 'stack'
van de flow.

```php
    'tasks' => [
        //example
        101 => \Workbench\App\Jobs\FlowExampleTask1Job::class,
        102 => \Workbench\App\Jobs\FlowExampleTask2Job::class,
    ...
```

### Task

De Task kan van twee verschillende types zijn: Job of invokable class. In beide gevallen moet de Flow zelf worden
meegegeven aan de Task.

Job: handig hierbij is om unique eigenschappen mee te geven aan de job Let op dat in de naam van de job 'Job' moet staan
anders wordt de job niet herkend als een job.

```php
    use mmerlijn\LaravelSalt\Jobs\Tasks\TaskJobTrait;
    public function handle()
    {
        //do something with the flow
    }
```

Invokable class: handig hierbij is om unique eigenschappen mee te geven aan de job

```php
    public function __invoke(protected Flow $flow)
    {
        //do something with the flow
    }
```

#### Uitvoeren van een taak

```php
$flow->run();
```

#### Uitvoeren van alle taken

```php
Flow::runAll();
```

#### Resultaat van een task

Succesvol uitgevoerd:

```php
//Taak is succesvol uitgevoerd, de flow gaat verder met de volgende taak
// - Volgende taak wordt direct uitgevoerd
$flow->done(
        int|string $task,
        int        $wait = 0,
        int|array  $runNext = 0,
        int|array  $skipTask = 0,
    );
```

Met problemen uitgevoerd:

```php
// Flow is mislukt, indien een FlowError wordt meegestuurd stopt de flow totdat de FlowError is opgelost. Indien een aantal minuten wordt meegestuurd wordt de flow na dat aantal minuten opnieuw uitgevoerd.
$flow->fail(
        int               $wait = 0,
        \Error|\Exception $exception = null,
        int|array         $runBefore = 0,
        int|array         $runBeforeAfterMaxAttempts = 0,
        int               $maxAttempts = 0,
        bool              $reset = false,
        int               $notifyAfterAttempts = 0,
        ?string           $solution = null,
        bool              $notifyAfterException = true,
        bool              $notifyAfterMaxAttempts = true,
        bool              $resetResponse = false,
        bool              $resetRequest = false
        );

```

## Development

Development list routes:

```bash 
vendor/bin/testbench route:list
```

Testing all tests:

```bash
./vendor/bin/pest

# or 
./vendor/bin/pest --parallel

# or testing one
./vendor/bin/test --filter=TestName

```
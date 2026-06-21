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
Flow::add(flow: <int|Enum>, payload: <Class|null>, wait: <int (minuten)>);
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
    public int $uniqueFor = 60;
    public int $tries = 1;
    public int $maxExceptions = 0;

    public function uniqueVia(): Repository
    {
        return Cache::driver('database');
    }
    public function uniqueId(): string
    {
        return 'flow-<nr>-'.$this->flow->id;
    }
    public function __construct(protected Flow $flow)
    {
        //do something with the flow
    }
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
$flow->done(wait: <int>(minuten)> );

// Er wordt een nieuwe taak aan de start van de stack toegevoegd
// - Volgende taak wordt direct uitgevoerd
$flow->prepend(<int|Enum|array> <int (minuten)>);
```

Met problemen uitgevoerd:

```php
// Opnieuw uitvoeren na backoff op basis van aantal pogingen
$flow->retry(<null|int (minuten)>);

// Flow is mislukt, indien een AppError wordt meegestuurd stopt de flow totdat de AppError is opgelost. Indien een aantal minuten wordt meegestuurd wordt de flow na dat aantal minuten opnieuw uitgevoerd.
$flow->fail(appError: <AppError|null>,<null|int (minuten)>);

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
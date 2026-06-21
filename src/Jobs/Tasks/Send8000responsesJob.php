<?php

namespace mmerlijn\LaravelSalt\Jobs\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use mmerlijn\LaravelSalt\Actions\Tasks\SendResponses\ResponseToFile;
use mmerlijn\LaravelSalt\Actions\Tasks\SendResponses\ResponseToHttp;
use mmerlijn\LaravelSalt\Actions\Tasks\SendResponses\ResponseToHttpMirth;
use mmerlijn\LaravelSalt\Actions\Tasks\SendResponses\ResponseToMirth;
use mmerlijn\LaravelSalt\Actions\Tasks\SendResponses\ResponseToMirthHttp;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Enums\SendTypeEnum;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Models\Flow;

class Send8000responsesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $maxExceptions = 0;

    public int $uniqueFor = 60;

    public function uniqueVia(): Repository
    {
        return Cache::driver('database');
    }

    public function __construct(public Flow $flow)
    {
    }

    public function uniqueId(): string
    {
        return 'flow-8000-' . $this->flow->id;
    }

    public function handle(): void
    {
        $max_attempts = 5;
        $sendType = config('laravel_salt.mirth_ports.' . $this->flow->payload->type[1], SendTypeEnum::MIRTH_TCP);
        try {
            if ($sendType == SendTypeEnum::MIRTH_TCP) {
                new ResponseToMirth()($this->flow->payload);
            } elseif ($sendType == SendTypeEnum::MIRTH_HTTP) {
                new ResponseToMirthHttp()($this->flow->payload);
            } elseif ($sendType == SendTypeEnum::FILE) {
                new   ResponseToFile()($this->flow->payload);
                $max_attempts = 100;
            } elseif ($sendType == SendTypeEnum::HTTP) {
                new ResponseToHttp()($this->flow->payload);
            }
            $this->flow->done(self::class);
        } catch (\Exception $e) {
            if ($this->flow->attempts > $max_attempts) {
                logger()->error("Error sending response for flow id {$this->flow->id} with type {$this->flow->payload->type}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                $this->flow->fail(new Error(
                    level: ErrorLevelEnum::SYSTEEMBEHEER,
                    fromObject: $this->flow->payload,
                    exception: $e,
                    solution: "Zijn alle porten open, is er een vreemde opmaak in het bericht, is er een netwerk fout?",
                    notify: true,
                    erroredClass: self::class,
                )->store());
            }
        }
        $this->flow->fail(null, 10); //probeer gewoon nog een keer
    }

}

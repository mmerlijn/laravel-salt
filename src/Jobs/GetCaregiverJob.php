<?php

namespace mmerlijn\LaravelSalt\Jobs;

use mmerlijn\LaravelSalt\Helpers\VektisGrabber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use mmerlijn\msgRepo\Enums\VektisType;

class GetCaregiverJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public VektisType $type, public string $agbcode)
    {
    }

    public function handle(): void
    {
        if(!config('laravel_salt.vektis',false)) {
            return;
        }
        try {
            $v = Validator::make(['agbcode' => $this->agbcode], [
                'agbcode' => ['required', 'size:8', 'not_in:00000000']
            ]);

            $v->validate();
        } catch (\Exception $e) {
            logger("GetCaregiver: ($this->type->value) Invalid AGBcode: $this->agbcode");
            return;
        }
        new VektisGrabber()($this->type, $this->agbcode);
    }
}

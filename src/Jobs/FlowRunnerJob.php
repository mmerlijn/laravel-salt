<?php

namespace mmerlijn\LaravelSalt\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use mmerlijn\LaravelSalt\Models\Flow;

class FlowRunnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $uniqueFor = 45;
    public function __construct()
    {
    }
    public function uniqueId(): string
    {
        return 'flow-runner';
    }
    public function handle(): void
    {
        foreach (Flow::where('try_after', '<=', now())->get() as $flow) {
            $flow->run();
        }
    }
}

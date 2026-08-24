<?php


namespace mmerlijn\LaravelSalt\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\UniqueFor;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use mmerlijn\LaravelSalt\Models\FlowError;

#[UniqueFor(45)]
class ClearNonRelatedFlowErrorsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 0;
    public int $maxExceptions = 0;

    public function uniqueVia(): Repository
    {
        return Cache::driver('database');
    }

    public function __construct()
    {
    }

    public function uniqueId(): string
    {
        return 'flow-error-cleaner';
    }

    public function handle(): void
    {
        foreach (FlowError::whereDoesntHave('flow')->cursor() as $flowError) {
            $flowError->delete();
        }

    }
}
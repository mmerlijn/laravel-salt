<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccessLog extends Model
{
    use MassPrunable;

    protected $connection = "mysql_dwh";

    protected $guarded = [];
    protected $table = "log_accesses";


    public function loggable(): MorphTo
    {
        return $this->morphTo("loggable");
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subYear());
    }
}

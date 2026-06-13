<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class ChangesLog extends Model
{
    use MassPrunable;

    protected $connection = "mysql_dwh";
    protected $table = "log_changes";

    protected $guarded = [];

    public function prunable(): Builder
    {
        return static::where('updated_at', '<=', now()->subYear());
    }
}

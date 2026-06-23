<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Http\Resources\FlowErrorResource;
use mmerlijn\LaravelSalt\Observers\FlowErrorObserver;
use Workbench\Database\Factories\FlowErrorFactory;

/**
 * @property int|ErrorLevelEnum $level
 * @property string $from_type
 * @property int $from_id
 * @property string $at_type
 * @property int $at_id
 * @property string $class
 * @property string|null $exception_class
 * @property string $solution
 * @property string $message
 * @property string $trace
 * @property bool $notify
 * @property array $notified
 * @property null|object $from
 * @property null|object $at
 */
#[UseResource(FlowErrorResource::class),ObservedBy(FlowErrorObserver::class)]
class FlowError extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    protected $fillable = [
        'app_error_id',
        'from',
        'at',
        'level',
        'solution',
        'message',
        'trace',
        'exception_class',
        'notify',
        'notified',
        'class',
    ];
    protected $table = 'flow_errors';

    protected function casts(): array
    {
        return [
            'notified' => 'array',
            'level' => ErrorLevelEnum::class,
            'notify' => 'boolean',
        ];
    }

    //Object where the problem is triggered from (eg. a model)
    public function from(): MorphTo
    {
        return $this->morphTo();
    }

    //Object where the problem can be solved
    public function at(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeLevel(Builder $query, int|ErrorLevelEnum $level): Builder
    {
        return $query->where('level', $level?->value ?? $level);
    }

    public function scopeNotifiable(Builder $query): Builder
    {
        return $query->where('notify', true);
    }

    public function scopeForClass(Builder $query, string $class): Builder
    {
        return $query->where('class', $class);
    }

    public function scopeWithExceptionClass(Builder $query): Builder
    {
        return $query->whereNotNull('exception_class');
    }

    public function flows(): HasMany
    {
        return $this->hasMany(Flow::class, 'flow_error_id');
    }
    public function prunable(): Builder
    {
        return static::query()->where('deleted_at', '<=', now()->minus(months: 9));
    }
    public function pruning(): void
    {
        $this->flows()?->update(['flow_error_id' => null]);
    }

    protected static function newFactory(): FlowErrorFactory
    {
        return FlowErrorFactory::new();
    }
}

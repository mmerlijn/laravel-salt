<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Observers\FlowResponseObserver;
use Workbench\Database\Factories\FlowResponseFactory;

/**
 * @property string $response
 * @property Carbon $response_at
 * @property int $patient_id
 * @property Patient $patient
 * @property string $request_nr
 * @property int $type
 * @property int $port
 */
#[ObservedBy(FlowResponseObserver::class)]
class FlowResponse extends Model
{
    use HasFactory;

    protected $table = 'flow_responses';

    protected $fillable = [
        'response',
        'response_at',
        'type',
        'port',
        'patient_id',
        'request_nr',
    ];

    protected function casts(): array
    {
        return [
            'response_at' => 'datetime',
        ];
    }

    public function flows(): MorphMany
    {
        return $this->morphMany(Flow::class, 'payload');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected static function newFactory(): FlowResponseFactory
    {
        return FlowResponseFactory::new();
    }
}

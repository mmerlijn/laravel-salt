<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Models\Traits\FlowModelTrait;
use mmerlijn\LaravelSalt\Observers\FlowExchangeObserver;
use Workbench\Database\Factories\FlowExchangeFactory;

/**
 * @property string $request
 * @property string $response
 * @property Carbon $request_at
 * @property Carbon $response_at
 * @property mixed $type
 * @property int $patient_id
 * @property string $request_nr
 * @property int $port
 * @property Patient $patient
 * @property int $labtrain_id
 * @property Flow[] $flows
 */
#[ObservedBy(FlowExchangeObserver::class)]
class FlowExchange extends Model
{
    use HasFactory, FlowModelTrait;

    protected $table = 'flow_exchanges';

    protected $fillable = [
        'request',
        'response',
        'request_at',
        'response_at',
        'type',
        'patient_id',
        'request_nr',
        'port',
        'labtrain_id',
    ];

    protected function casts(): array
    {
        return [
            'request_at' => 'datetime',
            'response_at' => 'datetime',
        ];
    }


    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected static function newFactory(): FlowExchangeFactory
    {
        return FlowExchangeFactory::new();
    }
}

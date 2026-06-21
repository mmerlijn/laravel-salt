<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Observers\FlowRequestObserver;
use Workbench\Database\Factories\FlowRequestFactory;

/**
 * @property string $request
 * @property Carbon $request_at
 * @property int $patient_id
 * @property string $request_nr
 * @property Patient $patient
 */
#[ObservedBy(FlowRequestObserver::class)]
class FlowRequest extends Model
{
    use HasFactory;

    protected $table = 'flow_requests';

    protected $fillable = [
        'type',
        'request',
        'request_at',
        'patient_id',
        'request_nr',
    ];

    protected function casts(): array
    {
        return [
            'request_at' => 'datetime',
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

    protected static function newFactory(): FlowRequestFactory
    {
        return FlowRequestFactory::new();
    }
}

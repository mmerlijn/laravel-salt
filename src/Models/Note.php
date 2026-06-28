<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Enums\NoteSubjectEnum;
use mmerlijn\LaravelSalt\Enums\NoteTypeEnum;
use mmerlijn\LaravelSalt\Http\Resources\NoteResource;
use Workbench\Database\Factories\NoteFactory;

/**
 * @property int $id
 * @property string $note
 * @property NoteTypeEnum $type
 * @property Carbon $created_at
 * @property int $created_by
 * @property User $creator
 */
#[UseResource(NoteResource::class)]
class Note extends Model
{
    use HasFactory, prunable;

    protected $table = "notes";

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => NoteTypeEnum::class,
            'subject' => NoteSubjectEnum::class,
            'delete_after' => 'timestamp',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function prunable(): Builder
    {
        return static::whereDate('delete_after', '<', now());
    }

    protected static function newFactory(): NoteFactory
    {
        return NoteFactory::new();
    }


}

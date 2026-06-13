<?php

namespace mmerlijn\LaravelSalt\Helpers\Traits;

use Illuminate\Support\Facades\Auth;
use mmerlijn\LaravelSalt\Models\ChangesLog;

trait StoreChangesTrait
{
    public function storeChanges($model, $cols, $type = 'close'): void
    {
        foreach ($cols as $col) {
            if ($model->isDirty($col)) {
                if ($model->getAttributes()[$col] != $model->getRawOriginal($col)) {
                    ChangesLog::create([
                        'record_id' => $model->id,
                        'type' => $type,
                        'field' => $col,
                        'old' => $model->getRawOriginal($col),
                        'new' => $model->getAttributes()[$col],
                        'by' => Auth::id() ?? 500,
                    ]);
                }
            }
        }
    }
}
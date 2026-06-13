<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $counter
 * @property int $size
 * @property string $prefix
 * @property string $extension
 */
class Counter extends Model
{
    use HasFactory;

    protected $table = 'tool_counters';
    protected $guarded = [];

    public function getNewFilename(): array
    {
        $this->counter++;
        $this->save();
        $file = ($this->size > strlen($this->counter)) ?
            str_pad($this->counter, $this->size, '0', STR_PAD_LEFT) :
            substr($this->counter, - $this->size);
        return [
            'filename' => $this->prefix . $file . "." . $this->extension, //name with extension
            'file' => $this->prefix . $file, //name without extension
            'counter' => $this->counter,
            'extension' => $this->extension,
        ];
    }

    public function next(): int
    {
        $this->counter++;
        $this->save();
        return $this->counter;
    }

    public function current(): int
    {
        return $this->counter;
    }

}

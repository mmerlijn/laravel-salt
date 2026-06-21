<?php

namespace mmerlijn\LaravelSalt\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Models\AppError;
use Throwable;
/**
 * @property ErrorLevelEnum $level
 * @property mixed $fromObject
 * @property mixed $atObject
 * @property string $message
 * @property mixed $exception
 */
class Error
{
    private string $trace = "";
    private ?string $exceptionClass = null;

    public static function forException(
        Throwable $exception,
        ErrorLevelEnum $level = ErrorLevelEnum::MENNO,
        ?Model $fromObject = null,
        ?Model $atObject = null,
        string $solution = "",
        ?bool $notify = false,
        ?string $erroredClass = null,
    ): self {
        return new self(
            level: $level,
            fromObject: $fromObject,
            atObject: $atObject,
            exception: $exception,
            solution: $solution,
            notify: $notify,
            erroredClass: $erroredClass,
        );
    }

    public static function forMessage(
        string $message,
        ErrorLevelEnum $level = ErrorLevelEnum::MENNO,
        ?Model $fromObject = null,
        ?Model $atObject = null,
        string $solution = "",
        ?bool $notify = false,
        ?string $erroredClass = null,
    ): self {
        return new self(
            level: $level,
            fromObject: $fromObject,
            atObject: $atObject,
            message: $message,
            solution: $solution,
            notify: $notify,
            erroredClass: $erroredClass,
        );
    }
    public function __construct(
        public ErrorLevelEnum $level,
        public ?Model         $fromObject=null,
        public ?Model         $atObject=null,
        public string         $message="",
        public ?Throwable     $exception=null,
        public string         $solution="",
        public ?bool          $notify=false,
        public ?string        $erroredClass=null,
    ){
        $this->fillOther();
    }


    private function fillOther():void{
        if($this->exception instanceof Throwable){
            $this->exceptionClass = $this->exception::class;
            if($this->message ==""){
                $this->message = $this->exception->getMessage();
            }
            $this->trace = $this->exception->getTraceAsString();
        }
    }
    public function exception(?Throwable $exception):self{
        $this->exception = $exception;
        $this->fillOther();
        return $this;
    }
    public function notify():self{
        $this->notify = true;
        return $this;
    }
    public function solution(mixed $solution):self{
        $this->solution = $solution;
        return $this;
    }
    public function fromObject(mixed $object):self{
        $this->fromObject = $object;
        return $this;
    }
    public function atObject(mixed $object):self{
        $this->atObject = $object;
        return $this;
    }
    public function message(string $message):self
    {
        $this->message = $message;
        return $this;
    }
    public function trace(string $trace):self
    {
        $this->trace = $trace;
        return $this;
    }

    public function save():AppError{
        return $this->store();
    }
    public function store():AppError
    {
        $appError = AppError::create([
            'level' => $this->level->value ?? 1,
            'message' => $this->message,
            'solution' => $this->solution,
            'trace' => $this->trace,
            'exception_class' => $this->exceptionClass,
            'notify' => $this->notify,
            'class' => $this->erroredClass,
            'notified' => [],
        ]);
        if($this->fromObject){
            $appError->from()->associate($this->fromObject);
        }
        if($this->atObject){
            $appError->at()->associate($this->atObject);
        }
        $appError->save();
        return $appError;

    }
}
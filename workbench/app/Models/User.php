<?php
namespace Workbench\App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Workbench\Database\Factories\UserFactory;
class User extends Authenticatable

{
use HasFactory, Notifiable;

protected $guarded = [];
protected static function newFactory(): UserFactory
{
    return UserFactory::new();
}
}
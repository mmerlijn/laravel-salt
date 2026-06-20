<?php

namespace Workbench\App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workbench\Database\Factories\UserFactory; // Als je straks een factory wilt gebruiken

class User extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];


    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}

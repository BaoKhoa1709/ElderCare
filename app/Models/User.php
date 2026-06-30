<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $primaryKey = 'uid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uid',
        'full_name',
        'email',
        'password',
        'gender',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'gender' => Gender::class,
            'password' => 'hashed',
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'networks_id',
        'wallet',
        'wallet_hex',
        'password',
    ];
}

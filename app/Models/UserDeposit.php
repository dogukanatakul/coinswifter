<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDeposit extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'coins_id',
        'user_banks_id',
        'amount',
        'unique_token'
    ];
}

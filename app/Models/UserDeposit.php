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
        'contracted_banks_id',
        'user_banks_id',
        'users_id',
        'amount',
        'currency',
        'sender_name',
        'iban',
        'date',
        'description',
        'tck_no',
        'status',
    ];
    protected $casts = [
        'amount' => 'string',
    ];
}

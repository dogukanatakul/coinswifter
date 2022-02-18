<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'parities_id',
        'user_transactions_id',
        'order_transactions_id',
        'users_id',
        'user_coins_id',
        'coins_id',
        'amount',
        'price',
    ];

}

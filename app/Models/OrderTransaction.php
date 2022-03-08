<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'parities_id',
        'buyer_user_id',
        'buyer_order_id',
        'seller_user_id',
        'seller_order_id',
        'price',
        'amount',
        'microtime',
    ];

    protected $casts = [
        'amount' => 'string',
    ];

    protected $hidden = [
        "id",
        'parities_id',
        'buyer_user_id',
        'seller_user_id',
        'buyer_order_id',
        'seller_order_id',
    ];
}

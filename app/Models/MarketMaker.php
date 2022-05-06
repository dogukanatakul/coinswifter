<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketMaker extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'parities_id',
        'btc_parities_id',
        'users_id',
        'buy_spread',
        'sell_spread',
        'buy_order_count',
        'sell_order_count',
        'btc_buy_spread',
        'btc_sell_spread',
        'btc_buy_order_count',
        'btc_sell_order_count',
        'min_token',
        'max_token',
        'scale_count',
        'price_scale_count',
        'btc_primary',
    ];
    protected $hidden = [
        'parities_id',
        'btc_parities_id',
        'users_id',
        'id',
    ];
}

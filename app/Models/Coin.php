<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coin extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'symbol',
        'networks_id',
        'contract',
        'token_type',
        'status',
        'transfer_min',
        'transfer_max',
        'commission_in',
        'commission_out',
        'commission_type',
        'settings',
        'promotion',
        'order',
        'info',
        'urls',
        'start_price',
        'start_time',
        'supply_max',
        'supply_total',
    ];

    protected $hidden = [
        'networks_id',
        'id',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'promotion' => 'array',
        'transfer_min' => 'string',
        'transfer_max' => 'string',
        'commission_in' => 'string',
        'commission_out' => 'string',
        'urls' => 'array',
        'start_price' => 'string',
    ];

    public function user_coin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserCoin::class, 'coins_id', 'id');
    }

    public function parity_coin(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Parity::class, 'coin_id', 'id');
    }

    public function network(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Network::class, 'id', 'networks_id');
    }

}

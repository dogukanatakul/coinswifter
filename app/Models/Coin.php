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
        'status',
        'transfer_min',
        'transfer_max',
        'commission_in',
        'commission_out',
        'commission_type',
        'settings',
        'promotion',
        'order'
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

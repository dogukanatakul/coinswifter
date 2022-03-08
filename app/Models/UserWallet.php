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

    public function user_coin(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserCoin::class, 'user_wallets_id', 'id');
    }

    public function network(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Network::class, 'id', 'networks_id');
    }

}

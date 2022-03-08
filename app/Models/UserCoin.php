<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCoin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'user_wallets_id',
        'coins_id',
        'balance_pure',
        'balance',
    ];

    protected $casts = [
        'balance_pure' => 'string',
        'balance' => 'string',
    ];

    public function user_coin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserCoin::class, 'users_id', 'users_id');
    }


    public function user_withdrawal_wallet_child(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserWithdrawalWalletChild::class, 'user_coins_id', 'id');
    }


    public function coin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Coin::class, 'id', 'coins_id');
    }


    public function parity_sources(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Parity::class, 'source_coin_id', 'coins_id');
    }

    public function parity_coins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Parity::class, 'coin_id', 'coins_id');
    }

    public function user_withdrawal_wallet(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserWithdrawalWallet::class, 'user_coins_id', 'id');
    }

    public function user_withdrawal(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserWithdrawal::class, 'user_coins_id', 'id');
    }

    public function user_wallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserWallet::class, 'id', 'user_wallets_id');
    }


}

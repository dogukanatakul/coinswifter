<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWithdrawalWallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'users_id',
        'to_user_id',
        'user_coins_id',
        'coins_id',
        'amount',
        'send_amount',
        'commission',
        'to',
        'status',
    ];

    protected $casts = [
        'amount' => 'string',
        'send_amount' => 'string',
        'commission' => 'string',
    ];

    protected $hidden = [
        'users_id',
        'to_user_id',
        'user_coins_id',
        'coins_id',
        'send_amount',
        'commission',
    ];

    public function user_withdrawal_wallet_child(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserWithdrawalWalletChild::class, 'user_withdrawal_wallets_id', 'id');
    }

    public function coin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Coin::class, 'id', 'coins_id');
    }
}

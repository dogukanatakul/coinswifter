<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWithdrawalWalletChild extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_withdrawal_wallets_id',
        'user_coins_id',
        'amount',
        'status',
        'txh',
        'error_answer',
        'multiply'
    ];

    protected $casts = [
        'multiply' => 'integer'
    ];

    public function user_coin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserCoin::class, 'id', 'user_coins_id');
    }

    public function user_withdrawal_wallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserWithdrawalWallet::class, 'id', 'user_withdrawal_wallets_id');
    }

    public function user_withdrawal_wallet_fee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserWithdrawalWalletFee::class, 'user_withdrawal_wallet_children_id', 'id');
    }

}

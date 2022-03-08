<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWithdrawalWalletFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_withdrawal_wallets_id',
        'user_withdrawal_wallet_children_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'string',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWithdrawal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'user_banks_id',
        'user_coins_id',
        'amount',
        'explanation',
        'status',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'amount' => 'string'
    ];

    public function user_bank(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserBank::class, 'id', 'user_banks_id');
    }
}

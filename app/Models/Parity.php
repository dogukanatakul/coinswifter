<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parity extends Model
{
    use HasFactory, SoftDeletes;

    // parite_ciftleri
    protected $primaryKey = 'id';

    protected $fillable = [
        'source_coin_id',
        'coin_id',
        'order',
        'status',
        'settings',
        'promotion'
    ];

    protected $hidden = [
        'id',
        'source_coin_id',
        'coins_id',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'promotion' => 'array',
    ];


    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'parities_id', 'id');
    }

    public function trades(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderTransaction::class, 'parities_id', 'id');
    }

    public function trade(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderTransaction::class, 'parities_id', 'id')->withTrashed();
    }

    public function source(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Coin::class, 'id', 'source_coin_id');
    }

    public function coin(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Coin::class, 'id', 'coin_id');
    }

    public function parity_price(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ParityPrice::class, 'parities_id', 'id');
    }

    public function commission(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ParityCommission::class, 'parities_id', 'id');
    }

    public function user_favorite(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserFavoritePairs::class, 'parities_id', 'id');
    }
}

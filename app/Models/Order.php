<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parities_id',
        'users_id',
        'trigger',
        'price',
        'amount',
        'amount_pure',
        'percent',
        'total',
        'type',
        'process',
        'microtime',
    ];

    protected $casts = [
        'trigger' => 'string',
        'amount' => 'string',
        'price' => 'string',
        'total' => 'string',
    ];

    protected $hidden = [
        'id',
        'parities_id',
        'users_id',
        'deleted_at',
        'updated_at',
    ];

    protected $attributes = [
        'amount_pure' => '',
    ];


    protected static function boot()
    {
        parent::boot();
        // auto-sets values on creation
        static::creating(function ($query) {
            $query->amount_pure = $query->amount;
        });
    }

    public function parity(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Parity::class, 'id', 'parities_id');
    }

    public function buying_trades()
    {
        return $this->hasMany(OrderTransaction::class, 'buyer_order_id', 'id');
    }

    public function selling_trades()
    {
        return $this->hasMany(OrderTransaction::class, 'seller_order_id', 'id');
    }


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'users_id');
    }

}

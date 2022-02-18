<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'banks_id',
        'branch_code',
        'branch_name',
        'account_number',
        'iban',
        'primary'
    ];

    protected $hidden = [
        'id',
        'users_id',
        'banks_id',
        'deleted_at',
        'updated_at',
    ];

    public function bank(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Bank::class, 'id', 'banks_id');
    }

}

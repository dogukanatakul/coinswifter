<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractedBank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'banks_id',
        'account_name',
        'iban',
        'account_number',
        'branch_code',
        'account_type'
    ];

    protected $hidden = [
        'banks_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    public function bank(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Bank::class, 'id', 'banks_id');
    }
}

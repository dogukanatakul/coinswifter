<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParityPrice extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';

    protected $fillable = [
        'parities_id',
        'value',
        'type',
        'source',
    ];

    protected $hidden = [
        'parities_id',
        'source',
        'created_at',
        'updated_at',
    ];

    public function parity(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Parity::class, 'id', 'parities_id');
    }

}

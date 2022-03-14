<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParityChart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parities_id',
        'type',
        'data',
    ];

    protected $casts = [
        'data' => 'array'
    ];
}

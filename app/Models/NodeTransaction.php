<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NodeTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'block_number',
        'from',
        'to',
        'contract',
        'txh',
        'value',
        'progress',
        'network',
        'status',
    ];

    protected $casts = [
        'value' => 'float',
    ];
}

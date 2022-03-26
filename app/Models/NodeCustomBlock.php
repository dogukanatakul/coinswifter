<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NodeCustomBlock extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'block_number',
        'network'
    ];
}

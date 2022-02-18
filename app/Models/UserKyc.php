<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserKyc extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'users_id',
        'user_addresses_id',
        'file_name',
        'file_extension',
        'file_size',
        'confirming_user_id',
        'status',
        'explanation',
        'type',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAgreement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'agreement',
    ];
}

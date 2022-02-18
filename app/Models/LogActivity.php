<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogActivity extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'subject',
        'url',
        'path',
        'method',
        'ip',
        'agent',
        'users_id',
        'status',
        'status_text',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'users_id');
    }
}

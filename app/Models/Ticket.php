<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id';
    protected $fillable = [
        'users_id',
        'category_id',
        'issue_id',
        'user_answered_id',
        'detail',
        'file_name',
        'file_extension',
        'status',
    ];
    public function category(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketCategory::class, 'id', 'category_id');
    }
    public function issue(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketIssue::class, 'id', 'issue_id');
    }
}

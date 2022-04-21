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
        'ticket_key',
        'users_id',
        'category_id',
        'subject_id',
        'user_answered_id',
        'status',
    ];
    protected $hidden = [
        'id',
    ];
    public function category(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketCategory::class, 'id', 'category_id');
    }
    public function subject(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TicketSubject::class, 'id', 'subject_id');
    }
}

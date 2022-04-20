<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMessage extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';

    protected $fillable = [
        'ticket_id',
        'users_answered_id',
        'message',
    ];
    protected $hidden = [
        'id',
        'ticket_id',
    ];
    public function ticket(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'id', 'ticket_id');
    }
    public function user():\Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(User::class, 'id', 'users_answered_id');
    }
}

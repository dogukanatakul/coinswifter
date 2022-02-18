<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use HasFactory, Notifiable, SoftDeletes, Authenticatable, CanResetPassword;

    protected $guarded = 'web';
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'name',
        'surname',
        'nationality',
        'tck_no',
        'pasaport_no',
        'birthday',
        'password',
        'type',
        'status',
        'referance_code'
    ];


    protected $hidden = [
        'password',
        'tck_no',
        'id',
    ];

    protected $attributes = [
        'referance_code' => '',
    ];


    protected static function boot()
    {
        parent::boot();
        // auto-sets values on creation
        static::creating(function ($query) {
            $query->referance_code = getRefererCode();
        });
    }

    public function contacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserContact::class, 'users_id', 'id');
    }

}

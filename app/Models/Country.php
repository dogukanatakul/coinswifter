<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'iso',
        'name_lowercase',
        'name',
        'iso3',
        'country_code',
        'phone_code'
    ];
}

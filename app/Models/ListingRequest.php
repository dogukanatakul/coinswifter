<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'coin_name',
        'network_info',
        'contract_adress',
        'coin_site',
        'whitepaper_url',
        'roadmap_url',
        'project_info',
        'maximum_supply',
        'listing_exchanges',
        'github_url',
        'coinmarketcap_url',
        'coingecko_url',
        'twitter_url',
        'telegram_url',
        'info',
    ];

}

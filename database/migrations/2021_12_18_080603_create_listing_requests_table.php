<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('coin_name')->nullable()->default(null);
            $table->string('network_info')->nullable()->default(null);
            $table->string('contract_adress')->nullable()->default(null);
            $table->string('coin_site')->nullable()->default(null);
            $table->string('whitepaper_url')->nullable()->default(null);
            $table->string('roadmap_url')->nullable()->default(null);
            $table->text('project_info')->nullable()->default(null);
            $table->decimal('maximum_supply', 38, 22)->default(0);
            $table->string('listing_exchanges')->nullable()->default(null);
            $table->string('github_url')->nullable()->default(null);
            $table->string('coinmarketcap_url')->nullable()->default(null);
            $table->string('coingecko_url')->nullable()->default(null);
            $table->string('twitter_url')->nullable()->default(null);
            $table->string('telegram_url')->nullable()->default(null);
            $table->text('info')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_requests');
    }
}

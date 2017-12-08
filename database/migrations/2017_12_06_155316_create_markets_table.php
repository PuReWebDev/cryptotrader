<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('MarketCurrency');
            $table->string('BaseCurrency');
            $table->string('MarketCurrencyLong');
            $table->string('BaseCurrencyLong');
            $table->string('MinTradeSize');
            $table->string('MarketName');
            $table->string('Notice')->nullable();
            $table->string('IsSponsored')->nullable();
            $table->boolean('IsActive');
            $table->string('LogoUrl')->nullable();
            $table->string('Created');
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
        Schema::dropIfExists('markets');
    }
}

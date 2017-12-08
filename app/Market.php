<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'markets';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'MarketCurrency',
        'BaseCurrency',
        'MarketCurrencyLong',
        'BaseCurrencyLong',
        'MinTradeSize',
        'MarketName',
        'IsActive',
        'Created',
        'Notice',
        'IsSponsored',
        'LogoUrl',
    ];
}

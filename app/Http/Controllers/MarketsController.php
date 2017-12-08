<?php

namespace App\Http\Controllers;

use App\library\Bittrex;
use App\Market;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarketsController extends Controller
{
    /**
     * @var Bittrex $client
     */
    private $client;

    /**
     * @return Bittrex
     */
    public function getClient(): Bittrex
    {
        return $this->client;
    }

    /**
     * @param Bittrex $client
     */
    public function setClient(Bittrex $client): void
    {
        $this->client = $client;
    }

    /**
     * OrderHistoryController constructor.
     */
    public function __construct()
    {
//        $this->middleware('auth');
        $this->setClient(new Bittrex(env('BITTREX_KEY', false), env('BITTREX_SECRET', false)));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        /** @var Market $markets */
        $markets = app(Market::class)->where('IsActive', 1)->get();

        return view('markets', ['markets' => $markets]);
    }

    /**
     * fetchMarkets
     *
     * @return string|void
     */
    private function fetchMarkets()
    {
        Log::info('Fetching Market Data - ' . Carbon::now());

        /** @var array $clientResponse */
        $clientResponse = $this->client->getMarkets();

        if ($clientResponse['success'] == true) {
            return $this->saveMarketData($clientResponse['result']);
        } else {
            Log::info('Failed client response: ' . $clientResponse);

            return 'There was an error retrieving Market Data';
        }
    }

    /**
     * saveMarketData
     *
     * @param array $marketData
     */
    private function saveMarketData(array $marketData)
    {
        /** @var Collection $data */
        $data = collect($marketData);

        foreach ($data->all() as $array) {

            app(Market::class)->updateOrCreate(
                [
                    'MarketCurrency'     => $array['MarketCurrency'],
                    'BaseCurrency'       => $array['BaseCurrency'],
                    'MarketCurrencyLong' => $array['MarketCurrencyLong'],
                    'BaseCurrencyLong'   => $array['BaseCurrencyLong'],
                    'MarketName'         => $array['MarketName'],
                ],
                [
                    'MinTradeSize' => $array['MinTradeSize'],
                    'Notice'       => $array['Notice'],
                    'IsSponsored'  => $array['IsSponsored'],
                    'IsActive'     => $array['IsActive'],
                    'LogoUrl'      => $array['LogoUrl'],
                    'Created'      => $array['Created'],
                ]
            );
        }

        Log::info('Saving Market Data Complete - ' . Carbon::now());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$this->fetchMarkets();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

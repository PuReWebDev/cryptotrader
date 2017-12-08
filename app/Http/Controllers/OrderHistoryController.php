<?php

namespace App\Http\Controllers;

use App\library\Bittrex;
use Illuminate\Http\Request;

/**
 * Class OrderHistoryController
 *
 * @package App\Http\Controllers
 */
class OrderHistoryController extends Controller
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
        $this->setClient(new Bittrex(env('BITTREX_KEY', false), env('BITTREX_SECRET', false)));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//        dump($client->getWithdrawalHistory('BTC'));
        dump($this->client->getOrderHistory());
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
        //
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

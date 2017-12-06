<?php
/**
 * Created by PhpStorm.
 * User: purewebdev
 * Date: 12/6/17
 * Time: 12:22 AM
 */
declare(strict_types=1);

namespace App\library;

use BrianFaust\Http\Http;

class Bittrex
{
    /**
     * The API key used for authentication.
     *
     * @var string
     */
    private $key;

    /**
     * The API secret used for authentication.
     *
     * @var string
     */
    private $secret;

    /**
     * Create a new Bittrex instance.
     *
     * @param string $key
     * @param string $secret
     */
    public function __construct(string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Used to get the open and available trading markets at Bittrex along with other meta data.
     *
     * @return array
     */
    public function getMarkets(): array
    {
        return $this->send('public/getmarkets');
    }

    /**
     * Used to get all supported currencies at Bittrex along with other meta data.
     *
     * @return array
     */
    public function getCurrencies(): array
    {
        return $this->send('public/getcurrencies');
    }

    /**
     * Used to get the current tick values for a market.
     *
     * @param string $market
     *
     * @return array
     */
    public function getTicker(string $market): array
    {
        return $this->send('public/getticker', compact('market'));
    }

    /**
     * Used to get the last 24 hour summary of all active exchanges.
     *
     * @return array
     */
    public function getMarketSummaries(): array
    {
        return $this->send('public/getmarketsummaries');
    }

    /**
     * Used to get the last 24 hour summary of all active exchanges.
     *
     * @param string $market
     *
     * @return array
     */
    public function getMarketSummary(string $market): array
    {
        return $this->send('public/getmarketsummary', compact('market'));
    }

    /**
     * Used to get retrieve the orderbook for a given market.
     *
     * @param string  $market
     * @param string  $type
     * @param int|int $depth
     *
     * @return array
     */
    public function getOrderBook(string $market, string $type, ?int $depth = 20): array
    {
        return $this->send('public/getorderbook', compact('market', 'type', 'depth'));
    }

    /**
     * Used to retrieve the latest trades that have occured for a specific market.
     *
     * @param string $market
     *
     * @return array
     */
    public function getMarketHistory(string $market): array
    {
        return $this->send('public/getmarkethistory', compact('market'));
    }

    /**
     * Used to place a buy order in a specific market.
     *
     * @param string $market
     * @param float  $quantity
     * @param float  $rate
     *
     * @return array
     */
    public function buyLimit(string $market, float $quantity, float $rate): array
    {
        return $this->send('market/buylimit', compact('market', 'quantity', 'rate'));
    }

    /**
     * Used to place an sell order in a specific market.
     *
     * @param string $market
     * @param float  $quantity
     * @param float  $rate
     *
     * @return array
     */
    public function sellLimit(string $market, float $quantity, float $rate): array
    {
        return $this->send('market/selllimit', compact('market', 'quantity', 'rate'));
    }

    /**
     * Used to cancel a buy or sell order.
     *
     * @param string $uuid
     *
     * @return array
     */
    public function cancel(string $uuid): array
    {
        return $this->send('market/cancel', compact('uuid'));
    }

    /**
     * Get all orders that you currently have opened.
     *
     * @param string|null $market
     *
     * @return array
     */
    public function getOpenOrders(?string $market = null): array
    {
        return $this->send('market/getopenorders', compact('market'));
    }

    /**
     * Used to retrieve all balances from your account.
     *
     * @return array
     */
    public function getBalances(): array
    {
        return $this->send('account/getbalances');
    }

    /**
     * Used to retrieve the balance from your account for a specific currency.
     *
     * @param string $currency
     *
     * @return array
     */
    public function getBalance(string $currency): array
    {
        return $this->send('account/getbalance', compact('currency'));
    }

    /**
     * Used to retrieve or generate an address for a specific currency.
     *
     * @param string $currency
     *
     * @return array
     */
    public function getDepositAddress(string $currency): array
    {
        return $this->send('account/getdepositaddress', compact('currency'));
    }

    /**
     * Used to withdraw funds from your account.
     *
     * @param string      $currency
     * @param float       $quantity
     * @param string      $address
     * @param string|null $paymentid
     *
     * @return array
     */
    public function withdraw(string $currency, float $quantity, string $address, ?string $paymentid = null): array
    {
        return $this->send('account/withdraw', compact('currency', 'quantity', 'address', 'paymentid'));
    }

    /**
     * Used to retrieve a single order by uuid.
     *
     * @param string $uuid
     *
     * @return array
     */
    public function getOrder(string $uuid): array
    {
        return $this->send('account/getorder', compact('uuid'));
    }

    /**
     * Used to retrieve your order history.
     *
     * @param string|null $market
     *
     * @return array
     */
    public function getOrderHistory(?string $market = null): array
    {
        return $this->send('account/getorderhistory', compact('market'));
    }

    /**
     * Used to retrieve your withdrawal history.
     *
     * @param string $currency
     *
     * @return array
     */
    public function getWithdrawalHistory(string $currency): array
    {
        return $this->send('account/getwithdrawalhistory');
    }

    /**
     * Create and send an HTTP request.
     *
     * @param string $path
     * @param array  $arguments
     *
     * @return array
     */
    private function send(string $path, array $arguments = []): array
    {
        $nonce = time();
        $uri = "https://bittrex.com/api/v1.1/{$path}?apikey={$this->key}&nonce={$nonce}";

        return Http::withHeaders(['apisign' => hash_hmac('sha512', $uri, $this->secret)])
            ->post($uri, array_filter($arguments))
            ->json();
    }
}
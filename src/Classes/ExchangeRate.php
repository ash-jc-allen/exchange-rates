<?php

namespace AshAllenDesign\ExchangeRates\Classes;

use Carbon\Carbon;
use GuzzleHttp\Client;

class ExchangeRate
{
    private RequestBuilder $requestBuilder;

    public function __construct(RequestBuilder $requestBuilder = null)
    {
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder(new Client());
    }

    public function currencies(): array
    {
        return $this->requestBuilder->makeRequest('symbols')['symbols'];
    }

    public function exchangeRate(string $from, string|array $to, Carbon $date = null): array
    {
        if ($date) {
            Validation::validateDate($date);
        }

        $symbols = is_string($to) ? $to : implode(',', $to);

        $queryParams = [
            'base'    => $from,
            'symbols' => $symbols,
        ];

        $requestPath = $date ? $date->format('Y-m-d') : 'latest';

        return $this->requestBuilder->makeRequest($requestPath, $queryParams)['rates'];
    }

    public function exchangeRateBetweenDateRange(string $from, string|array $to, Carbon $startDate, Carbon $endDate): array
    {
        Validation::validateStartAndEndDates($startDate, $endDate);

        $symbols = is_string($to) ? $to : implode(',', $to);

        $queryParams = [
            'base'       => $from,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
            'symbols'    => $symbols,
        ];

        return $this->requestBuilder->makeRequest('/timeseries', $queryParams)['rates'];
    }

    public function convert(int $amount, string $from, string|array $to, Carbon $date = null): string|array
    {
        if ($date) {
            Validation::validateDate($date);
        }

        if (is_string($to)) {
            $exchangeRates = $this->exchangeRate($from, $to, $date);

            return $this->convertMoney($amount, $exchangeRates[$to]);
        }

        $converted = [];

        foreach ($this->exchangeRate($from, $to, $date) as $currencyCode => $exchangeRate) {
            $converted[$currencyCode] =  bcmul($amount, $exchangeRate, 8);
        }

        return $converted;
    }

    public function convertBetweenDateRange(int $amount, string $from, string|array $to, Carbon $startDate, Carbon $endDate): array
    {
        $exchangeRates = $this->exchangeRateBetweenDateRange($from, $to, $startDate, $endDate);

        if (is_string($to)) {
            return $this->convertSingleCurrencyPairOverDateRange($amount, $to, $exchangeRates);
        }

        return $this->convertMultipleCurrenciesOverDateRange($amount, $exchangeRates);
    }

    private function convertSingleCurrencyPairOverDateRange(int $amount, string $to, array $exchangeRates): array
    {
        $conversions = [];

        foreach ($exchangeRates as $date => $exchangeRate) {
            $conversions[$date] = $this->convertMoney($amount, $exchangeRate[$to]);
        }

        return $conversions;
    }

    private function convertMultipleCurrenciesOverDateRange(int $amount, array $exchangeRates): array
    {
        $conversions = [];

        foreach ($exchangeRates as $date => $exchangeRate) {
            foreach ($exchangeRate as $currency => $rate) {
                $conversions[$date][$currency] = $this->convertMoney($amount, $rate);
            }
        }

        return $conversions;
    }

    private function convertMoney(string $amount, string $exchangeRate): string
    {
        return bcmul($amount, $exchangeRate, 8);
    }
}
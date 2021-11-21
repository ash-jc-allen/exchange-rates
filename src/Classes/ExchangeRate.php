<?php

namespace AshAllenDesign\ExchangeRates\Classes;

use Carbon\Carbon;
use GuzzleHttp\Client;

class ExchangeRate
{
    private RequestBuilder $requestBuilder;

    /**
     * @param RequestBuilder|null $requestBuilder
     */
    public function __construct(RequestBuilder $requestBuilder = null)
    {
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder(new Client());
    }

    /**
     * Get all the available supported currencies.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function currencies(): array
    {
        return $this->requestBuilder->makeRequest('symbols')['symbols'];
    }

    /**
     * Find and return the exchange rate between currencies. If no date is
     * passed as the third parameter, today's exchange rate will be used.
     *
     * @param string $from
     * @param string|array $to
     * @param \Carbon\Carbon|null $date
     * @return string|array
     * @throws \AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function exchangeRate(string $from, string|array $to, Carbon $date = null): string|array
    {
        if ($date) {
            Validation::validateDate($date);
        }

        $symbols = is_string($to)
            ? $to
            : implode(',', $to);

        $queryParams = [
            'base'    => $from,
            'symbols' => $symbols,
        ];

        $requestPath = $date
            ? $date->format('Y-m-d')
            : 'latest';

        $rates = $this->requestBuilder->makeRequest($requestPath, $queryParams)['rates'];

        if (is_string($to)) {
            return $rates[$to];
        }

        return array_map(static fn (string $item): string => $item, $rates);
    }

    /**
     * Find and return the exchange rate between currencies between a given
     * date range.
     *
     * @param string $from
     * @param string|array $to
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return array
     * @throws \AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
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

    /**
     * Convert a monetary value from one currency to another. If no date is
     * passed as the third parameter, today's exchange rate will be used.
     *
     * @param int $amount
     * @param string $from
     * @param string|array $to
     * @param \Carbon\Carbon|null $date
     * @return string|array
     * @throws \AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function convert(int $amount, string $from, string|array $to, Carbon $date = null): string|array
    {
        if ($date) {
            Validation::validateDate($date);
        }

        $exchangeRates = $this->exchangeRate($from, $to, $date);

        if (is_string($to)) {
            return $this->convertMoney($amount, $exchangeRates);
        }

        $converted = [];

        foreach ($exchangeRates as $currencyCode => $exchangeRate) {
            $converted[$currencyCode] = $this->convertMoney($amount, $exchangeRate);
        }

        return $converted;
    }

    /**
     * Convert monetary values from one currency to another using the exchange
     * rates between a given date range.
     *
     * @param int $amount
     * @param string $from
     * @param string|array $to
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return array
     * @throws \AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function convertBetweenDateRange(int $amount, string $from, string|array $to, Carbon $startDate, Carbon $endDate): array
    {
        $to = is_array($to) ? $to : [$to];

        $exchangeRates = $this->exchangeRateBetweenDateRange($from, $to, $startDate, $endDate);

        return $this->convertCurrenciesOverDateRange($amount, $exchangeRates);
    }

    private function convertCurrenciesOverDateRange(int $amount, array $exchangeRates): array
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

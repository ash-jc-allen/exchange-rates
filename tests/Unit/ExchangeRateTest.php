<?php

namespace AshAllenDesign\ExchangeRates\Tests\Unit;

use AshAllenDesign\ExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\ExchangeRates\Classes\RequestBuilder;
use AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ExchangeRateTest extends TestCase
{
    /** @test */
    public function exchange_rate_for_a_single_currency_pair_for_today_is_returned_if_no_date_parameter_is_passed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['latest', [
                'base'    => 'GBP',
                'symbols' => 'EUR',
            ]])
            ->andReturn($this->mockResponseForTodayForSingleCurrency());

        self::assertSame(
            '1.186864',
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', 'EUR'),
        );
    }

    /** @test */
    public function exchange_rate_for_multiple_currencies_for_today_is_returned_if_no_date_parameter_is_passed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['latest', [
                'base'    => 'GBP',
                'symbols' => 'EUR,USD',
            ]])
            ->andReturn($this->mockResponseForTodayForMultipleCurrencies());

        self::assertSame(
            [
                'EUR' => '1.186864',
                'USD' => '1.376395',
            ],
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', ['EUR', 'USD']),
        );
    }

    /** @test */
    public function exchange_rate_for_a_single_currency_pair_in_the_past_is_returned_if_a_date_parameter_is_passed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['2021-10-25', [
                'base'    => 'GBP',
                'symbols' => 'EUR',
            ]])
            ->andReturn($this->mockResponseForYesterdayForSingleCurrency());

        self::assertSame(
            '1.186176',
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', 'EUR', Carbon::create(2021, 10, 25)),
        );
    }

    /** @test */
    public function exchange_rate_for_multiple_currencies_in_the_past_is_returned_if_a_date_parameter_is_passed(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['2021-10-25', [
                'base'    => 'GBP',
                'symbols' => 'EUR,USD',
            ]])
            ->andReturn($this->mockResponseForYesterdayForMultipleCurrencies());

        self::assertSame(
            [
                'EUR' => '1.186176',
                'USD' => '1.377304',
            ],
            (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', ['EUR', 'USD'], Carbon::create(2021, 10, 25)),
        );
    }

    /** @test */
    public function exception_is_thrown_if_the_date_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRate('GBP', 'EUR', Carbon::now()->addDay());
    }

    private function mockResponseForTodayForSingleCurrency(): array
    {
        return [
            'motd'    => [
                'msg' => 'If you or your company use this project or like what we doing, please consider backing us so we can continue maintaining and evolving this project.',
                'url' => 'https://exchangerate.host/#/donate',
            ],
            'success' => true,
            'base'    => 'GBP',
            'date'    => '2021-10-26',
            'rates'   => [
                'EUR' => 1.186864,
            ],
        ];
    }

    private function mockResponseForYesterdayForSingleCurrency(): array
    {
        return [
            'motd'       => [
                'msg' => 'If you or your company use this project or like what we doing, please consider backing us so we can continue maintaining and evolving this project.',
                'url' => 'https://exchangerate.host/#/donate',
            ],
            'success'    => true,
            'historical' => true,
            'base'       => 'GBP',
            'date'       => '2021-10-25',
            'rates'      => [
                'EUR' => 1.186176,
            ],
        ];
    }

    private function mockResponseForTodayForMultipleCurrencies(): array
    {
        return [
            'motd'    => [
                'msg' => 'If you or your company use this project or like what we doing, please consider backing us so we can continue maintaining and evolving this project.',
                'url' => 'https://exchangerate.host/#/donate',
            ],
            'success' => true,
            'base'    => 'GBP',
            'date'    => '2021-10-26',
            'rates'   => [
                'EUR' => 1.186864,
                'USD' => 1.376395,
            ],
        ];
    }

    private function mockResponseForYesterdayForMultipleCurrencies(): array
    {
        return [
            'motd'       => [
                'msg' => 'If you or your company use this project or like what we doing, please consider backing us so we can continue maintaining and evolving this project.',
                'url' => 'https://exchangerate.host/#/donate',
            ],
            'success'    => true,
            'historical' => true,
            'base'       => 'GBP',
            'date'       => '2021-10-25',
            'rates'      => [
                'EUR' => 1.186176,
                'USD' => 1.377304,
            ],
        ];
    }
}

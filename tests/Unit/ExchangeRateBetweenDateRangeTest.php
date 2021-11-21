<?php

namespace AshAllenDesign\ExchangeRates\Tests\Unit;

use AshAllenDesign\ExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\ExchangeRates\Classes\RequestBuilder;
use AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ExchangeRateBetweenDateRangeTest extends TestCase
{
    /** @test */
    public function exchange_rates_between_date_range_are_returned_for_a_single_currency(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['/timeseries', [
                'base'       => 'GBP',
                'start_date' => '2021-10-19',
                'end_date'   => '2021-10-25',
                'symbols'    => 'EUR',
            ]])
            ->andReturn($this->mockResponseForSingleCurrencyPair());

        self::assertEquals(
            $this->expectedForSingleCurrencyPair(),
            (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange(
                'GBP',
                'EUR',
                Carbon::create(2021, 10, 19),
                Carbon::create(2021, 10, 25)
            ),
        );
    }

    /** @test */
    public function exchange_rates_between_date_range_are_returned_for_multiple_currencies(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->once()
            ->withArgs(['/timeseries', [
                'base'       => 'GBP',
                'start_date' => '2021-10-19',
                'end_date'   => '2021-10-25',
                'symbols'    => 'EUR,USD',
            ]])
            ->andReturn($this->mockResponseForMultipleCurrencies());

        self::assertEquals(
            $this->expectedForMultipleCurrencies(),
            (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange(
                'GBP',
                ['EUR', 'USD'],
                Carbon::create(2021, 10, 19),
                Carbon::create(2021, 10, 25)
            ),
        );
    }

    /** @test */
    public function exception_is_thrown_if_the_start_date_parameter_passed_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->addDay(), Carbon::now()->addDays(2));
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_parameter_passed_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->subDay(), Carbon::now()->addDays(2));
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_is_before_the_start_date(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate($requestBuilderMock))->exchangeRateBetweenDateRange('GBP', 'EUR', Carbon::now()->subDay(), Carbon::now()->subDays(2));
    }

    private function mockResponseForSingleCurrencyPair(): array
    {
        return [
            'motd'       => [
                'msg' => 'If you or your company use this project or like what we doing, please consider backing us so we can continue maintaining and evolving this project.',
                'url' => 'https://exchangerate.host/#/donate',
            ],
            'success'    => true,
            'timeseries' => true,
            'base'       => 'GBP',
            'start_date' => '2021-10-19',
            'end_date'   => '2021-10-25',
            'rates'      => [
                '2021-10-19' => [
                    'EUR' => 1.186206,
                ],
                '2021-10-20' => [
                    'EUR' => 1.18663,
                ],
                '2021-10-21' => [
                    'EUR' => 1.18649,
                ],
                '2021-10-22' => [
                    'EUR' => 1.181421,
                ],
                '2021-10-23' => [
                    'EUR' => 1.181848,
                ],
                '2021-10-24' => [
                    'EUR' => 1.1813,
                ],
                '2021-10-25' => [
                    'EUR' => 1.186176,
                ],
            ],
        ];
    }

    private function mockResponseForMultipleCurrencies(): array
    {
        return [
            'motd'       => [
                'msg' => 'If you or your company use this project or like what we doing, please consider backing us so we can continue maintaining and evolving this project.',
                'url' => 'https://exchangerate.host/#/donate',
            ],
            'success'    => true,
            'timeseries' => true,
            'base'       => 'GBP',
            'start_date' => '2021-10-19',
            'end_date'   => '2021-10-25',
            'rates'      => [
                '2021-10-19' => [
                    'EUR' => 1.186206,
                    'USD' => 1.381227,
                ],
                '2021-10-20' => [
                    'EUR' => 1.18663,
                    'USD' => 1.382278,
                ],
                '2021-10-21' => [
                    'EUR' => 1.18649,
                    'USD' => 1.378447,
                ],
                '2021-10-22' => [
                    'EUR' => 1.181421,
                    'USD' => 1.375148,
                ],
                '2021-10-23' => [
                    'EUR' => 1.181848,
                    'USD' => 1.375865,
                ],
                '2021-10-24' => [
                    'EUR' => 1.1813,
                    'USD' => 1.375172,
                ],
                '2021-10-25' => [
                    'EUR' => 1.186176,
                    'USD' => 1.377304,
                ],
            ],
        ];
    }

    private function expectedForSingleCurrencyPair(): array
    {
        return [
            '2021-10-19' => [
                'EUR' => '1.186206',
            ],
            '2021-10-20' => [
                'EUR' => '1.18663',
            ],
            '2021-10-21' => [
                'EUR' => '1.18649',
            ],
            '2021-10-22' => [
                'EUR' => '1.181421',
            ],
            '2021-10-23' => [
                'EUR' => '1.181848',
            ],
            '2021-10-24' => [
                'EUR' => '1.1813',
            ],
            '2021-10-25' => [
                'EUR' => '1.186176',
            ],
        ];
    }

    private function expectedForMultipleCurrencies(): array
    {
        return [
            '2021-10-19' => [
                'EUR' => '1.186206',
                'USD' => '1.381227',
            ],
            '2021-10-20' => [
                'EUR' => '1.18663',
                'USD' => '1.382278',
            ],
            '2021-10-21' => [
                'EUR' => '1.18649',
                'USD' => '1.378447',
            ],
            '2021-10-22' => [
                'EUR' => '1.181421',
                'USD' => '1.375148',
            ],
            '2021-10-23' => [
                'EUR' => '1.181848',
                'USD' => '1.375865',
            ],
            '2021-10-24' => [
                'EUR' => '1.1813',
                'USD' => '1.375172',
            ],
            '2021-10-25' => [
                'EUR' => '1.186176',
                'USD' => '1.377304',
            ],
        ];
    }
}

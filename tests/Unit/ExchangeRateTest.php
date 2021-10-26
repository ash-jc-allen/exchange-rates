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

    }

    /** @test */
    public function exchange_rate_for_multiple_currencies_for_today_is_returned_if_a_date_parameter_is_passed(): void
    {

    }

    /** @test */
    public function exchange_rate_for_a_single_currency_pair_in_the_past_is_returned_if_no_date_parameter_is_passed(): void
    {

    }

    /** @test */
    public function exchange_rate_for_multiple_currencies_in_the_past_is_returned_if_a_date_parameter_is_passed(): void
    {

    }

    /** @test */
    public function exception_is_thrown_if_the_date_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate())->exchangeRate('GBP', 'EUR', Carbon::now()->addDay());
    }
}

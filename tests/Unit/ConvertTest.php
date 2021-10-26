<?php

namespace AshAllenDesign\ExchangeRates\Tests\Unit;

use AshAllenDesign\ExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\ExchangeRates\Classes\RequestBuilder;
use AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ConvertTest extends TestCase
{
    /** @test */
    public function converted_value_for_today_is_returned_if_no_date_parameter_is_passed(): void
    {

    }

    /** @test */
    public function converted_value_in_the_past_is_returned_if_the_date_parameter_is_passed(): void
    {

    }

    /** @test */
    public function converted_values_are_returned_for_today_with_multiple_currencies(): void
    {

    }

    /** @test */
    public function exception_is_thrown_if_the_date_parameter_passed_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate())->convert(100, 'GBP', 'EUR', Carbon::now()->addDay());
    }
}

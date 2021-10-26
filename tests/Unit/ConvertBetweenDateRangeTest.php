<?php

namespace AshAllenDesign\ExchangeRates\Tests\Unit;

use AshAllenDesign\ExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\ExchangeRates\Classes\RequestBuilder;
use AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException;
use AshAllenDesign\ExchangeRates\Tests\TestCase;
use Carbon\Carbon;

class ConvertBetweenDateRangeTest extends TestCase
{
    /** @test */
    public function converted_values_between_date_range_are_returned_for_a_single_currency(): void
    {

    }

    /** @test */
    public function converted_values_between_date_range_are_returned_for_multiple_currencies(): void
    {

    }

    /** @test */
    public function exception_is_thrown_if_the_start_date_parameter_passed_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate())->convertBetweenDateRange(100, 'GBP', 'EUR', Carbon::now()->addDay(), Carbon::now()->addDays(2));
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_parameter_passed_is_in_the_future(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate())->convertBetweenDateRange(100, 'GBP', 'EUR', Carbon::now()->subDay(), Carbon::now()->addDays(2));
    }

    /** @test */
    public function exception_is_thrown_if_the_end_date_is_before_the_start_date(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')->never();

        $this->expectException(InvalidDateException::class);

        (new ExchangeRate())->convertBetweenDateRange(100, 'GBP', 'EUR', Carbon::now()->subDay(), Carbon::now()->subDays(2));
    }
}

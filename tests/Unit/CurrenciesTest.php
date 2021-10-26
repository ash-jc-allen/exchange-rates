<?php

namespace AshAllenDesign\ExchangeRates\Tests\Unit;

use AshAllenDesign\ExchangeRates\Classes\ExchangeRate;
use AshAllenDesign\ExchangeRates\Classes\RequestBuilder;
use AshAllenDesign\ExchangeRates\Tests\TestCase;

class CurrenciesTest extends TestCase
{
    /** @test */
    public function currencies_are_returned(): void
    {
        $requestBuilderMock = \Mockery::mock(RequestBuilder::class);

        $requestBuilderMock->shouldReceive('makeRequest')
            ->withArgs(['symbols'])
            ->once()
            ->andReturn($this->mockResponse());

        self::assertEquals(
            $this->expectedResponse(),
            (new ExchangeRate($requestBuilderMock))->currencies(),
        );
    }

    private function mockResponse(): array
    {
        return [
            'motd'    => [
                'msg' => 'If you or your company use this project or like what we doing, please consider backing us so we can continue maintaining and evolving this project.',
                'url' => 'https://exchangerate.host/#/donate'
            ],
            'success' => 1,
            'symbols' => [
                'AED' => [
                    'description' => 'United Arab Emirates Dirham',
                    'code'        => 'AED',
                ],
                'AFN' => [
                    'description' => 'Afghan Afghani',
                    'code'        => 'AFN'
                ],
                'ALL' => [
                    'description' => 'Albanian Lek',
                    'code'        => 'ALL',
                ]
            ]
        ];
    }

    private function expectedResponse(): array
    {
        return [
            'AED' => [
                'description' => 'United Arab Emirates Dirham',
                'code'        => 'AED',
            ],
            'AFN' => [
                'description' => 'Afghan Afghani',
                'code'        => 'AFN'
            ],
            'ALL' => [
                'description' => 'Albanian Lek',
                'code'        => 'ALL',
            ]
        ];
    }
}

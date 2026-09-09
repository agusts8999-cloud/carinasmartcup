<?php

use App\Support\Money;

it('parses indonesian idr thousands with dot separator', function () {
    expect(Money::parseIdr('29.000'))->toBe(29000.0)
        ->and(Money::parseIdr('2.740'))->toBe(2740.0)
        ->and(Money::parseIdr('350.600'))->toBe(350600.0)
        ->and(Money::parseIdr('1.234.567'))->toBe(1234567.0);
});

it('parses comma thousands separator', function () {
    expect(Money::parseIdr('15,000'))->toBe(15000.0)
        ->and(Money::parseIdr('1,234,567'))->toBe(1234567.0);
});

it('parses plain numeric and decimal values', function () {
    expect(Money::parseIdr('15000'))->toBe(15000.0)
        ->and(Money::parseIdr(29000))->toBe(29000.0)
        ->and(Money::parseIdr('15,5'))->toBe(15.5)
        ->and(Money::parseIdr('1.234.567,89'))->toBe(1234567.89);
});

it('formats idr without decimal places', function () {
    expect(Money::formatIdr(29000))->toBe('29.000')
        ->and(Money::formatIdr(15000))->toBe('15.000');
});

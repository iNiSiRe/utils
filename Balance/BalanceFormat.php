<?php

namespace PrivateDev\Utils\Balance;

class BalanceFormat
{
    /**
     * Для фикса чисел с плавающей точкой
     * https://bio-gram.myjetbrains.com/youtrack/issue/BG-4223
     * https://bio-gram.myjetbrains.com/youtrack/issue/BG-4976
     */
    const CORRECTION_FLOAT_VALUE = 0.0000000001;

    const PRECISIONS_MAP = [
        self::CODE_USD => 2,
        self::CODE_RUB => 2,
        self::CODE_EUR => 2,
        self::CODE_UAH => 2,
        self::CODE_KZT => 2,
        self::CODE_IDR => 2,
        self::CODE_THB => 2,
        self::CODE_INR => 2,
        self::CODE_BRL => 2,
        self::CODE_TRY => 2,
        self::CODE_VND => 0,
        self::CODE_JPY => 0,

        // Crypto
        self::CODE_BTC => 8,
        self::CODE_BCH => 8,
        self::CODE_TRX => 6,
        self::CODE_ETH => 18,
        self::CODE_XRP => 6,
        self::CODE_LTC => 8,
        self::CODE_USDT => 6,
    ];

    const CODE_USD = 'USD';
    const CODE_RUB = 'RUB';
    const CODE_EUR = 'EUR';
    const CODE_UAH = 'UAH';
    const CODE_KZT = 'KZT';
    const CODE_IDR = 'IDR';
    const CODE_THB = 'THB';
    const CODE_INR = 'INR';
    const CODE_VND = 'VND';
    const CODE_BRL = 'BRL';
    const CODE_TRY = 'TRY';
    const CODE_JPY = 'JPY';

    // Crypto
    const CODE_BTC = 'BTC';
    const CODE_BCH = 'BCH';
    const CODE_TRX = 'TRX';
    const CODE_ETH = 'ETH';
    const CODE_XRP = 'XRP';
    const CODE_LTC = 'LTC';
    const CODE_USDT = 'USDT';

    /**
     * @param int    $isoBalance
     * @param string $currency
     *
     * @return float
     */
    static public function toNative($isoBalance, string $currency = self::CODE_USD) : float
    {
        $precision = self::PRECISIONS_MAP[$currency] ?? self::PRECISIONS_MAP[self::CODE_USD];

        if ($precision === 0) {
            return $isoBalance;
        }

        return round($isoBalance / pow(10, $precision), $precision);
    }

    /**
     * @param float  $balance
     * @param string $currency
     *
     * @return int
     */
    static function toISO($balance, string $currency = self::CODE_USD) : int
    {
        $precision = self::PRECISIONS_MAP[$currency] ?? self::PRECISIONS_MAP[self::CODE_USD];

        if ($precision === 0) {
            return $balance;
        }

        return (int) (
            (self::CORRECTION_FLOAT_VALUE + $balance) * pow(10, $precision)
        );
    }
}
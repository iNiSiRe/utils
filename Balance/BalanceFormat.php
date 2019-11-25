<?php

namespace PrivateDev\Utils\Balance;

class BalanceFormat
{
    const PRECISIONS_MAP = [
        self::CODE_USD => 2,
        self::CODE_RUB => 2,
        self::CODE_EUR => 2,
        self::CODE_UAH => 2,
        self::CODE_KZT => 2,
        self::CODE_IDR => 2,

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
    static public function toNative(int $isoBalance, string $currency = self::CODE_USD) : float
    {
        $precision = self::PRECISIONS_MAP[$currency] ?? self::PRECISIONS_MAP[self::CODE_USD];

        return round($isoBalance / pow(10, $precision), $precision);
    }

    /**
     * @param float  $balance
     * @param string $currency
     *
     * @return int
     */
    static function toISO(float $balance, string $currency = self::CODE_USD) : int
    {
        return (int) ($balance * pow(10, self::PRECISIONS_MAP[$currency] ?? self::PRECISIONS_MAP[self::CODE_USD]));
    }
}
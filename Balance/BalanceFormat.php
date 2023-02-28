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
        self::CODE_USD       => 2,
        self::CODE_RUB       => 2,
        self::CODE_EUR       => 2,
        self::CODE_UAH       => 2,
        self::CODE_AUD       => 2,
        self::CODE_NOK       => 2,
        self::CODE_KZT       => 2,
        self::CODE_IDR       => 2,
        self::CODE_THB       => 2,
        self::CODE_INR       => 2,
        self::CODE_BRL       => 2,
        self::CODE_TRY       => 2,
        self::CODE_PHP       => 2,
        self::CODE_VND       => 0,
        self::CODE_JPY       => 0,

        // Fake
        self::CODE_FAKE_USD  => 2,
        self::CODE_FAKE_RUB  => 2,
        self::CODE_FAKE_EUR  => 2,
        self::CODE_FAKE_UAH  => 2,
        self::CODE_FAKE_KZT  => 2,
        self::CODE_FAKE_IDR  => 2,
        self::CODE_FAKE_THB  => 2,
        self::CODE_FAKE_INR  => 2,
        self::CODE_FAKE_BRL  => 2,
        self::CODE_FAKE_TRY  => 2,
        self::CODE_FAKE_PHP  => 2,
        self::CODE_FAKE_VND  => 0,
        self::CODE_FAKE_JPY  => 0,

        // Crypto
        self::CODE_BTC       => 8,
        self::CODE_BCH       => 8,
        self::CODE_TRX       => 6,
        self::CODE_ETH       => 18,
        self::CODE_XRP       => 6,
        self::CODE_LTC       => 8,
        self::CODE_USDT      => 6,

        // Fake crypto
        self::CODE_FAKE_BTC  => 8,
        self::CODE_FAKE_BCH  => 8,
        self::CODE_FAKE_TRX  => 6,
        self::CODE_FAKE_ETH  => 18,
        self::CODE_FAKE_XRP  => 6,
        self::CODE_FAKE_LTC  => 8,
        self::CODE_FAKE_USDT => 6,
    ];

    const CODE_USD = 'USD';
    const CODE_RUB = 'RUB';
    const CODE_EUR = 'EUR';
    const CODE_UAH = 'UAH';
    const CODE_AUD = 'AUD';
    const CODE_NOK = 'NOK';
    const CODE_KZT = 'KZT';
    const CODE_IDR = 'IDR';
    const CODE_THB = 'THB';
    const CODE_INR = 'INR';
    const CODE_VND = 'VND';
    const CODE_BRL = 'BRL';
    const CODE_TRY = 'TRY';
    const CODE_JPY = 'JPY';
    const CODE_PHP = 'PHP';

    // Crypto
    const CODE_BTC = 'BTC';
    const CODE_BCH = 'BCH';
    const CODE_TRX = 'TRX';
    const CODE_ETH = 'ETH';
    const CODE_XRP = 'XRP';
    const CODE_LTC = 'LTC';
    const CODE_USDT = 'USDT';

    // Fake currencies
    // md5('F_USD')
    const CODE_FAKE_USD = '86115e24d4dea71566b6eb6117cefa49';
    const CODE_FAKE_RUB = '5ac60634231471660fd08830b6ebb997';
    const CODE_FAKE_EUR = '7d2cf9124ae1ebd8972fe3654fc4f7dc';
    const CODE_FAKE_UAH = 'b633021d769d5f8844021281f3c8c902';
    const CODE_FAKE_KZT = 'dddc1184cff2c192a243daeecc055919';
    const CODE_FAKE_IDR = '00930360c18ec12cee65c8d79af5adf8';
    const CODE_FAKE_JPY = 'e9217d74f3b34f209507886679b51dd7';
    const CODE_FAKE_NOK = '9da9d122fdf01b1db2816592e00f28ad';
    const CODE_FAKE_INR = '23fd993377c00f9ee1d39de370953911';
    const CODE_FAKE_NZD = 'a6c741b0624c718598643c4df8f79aa2';
    const CODE_FAKE_AUD = 'db8c0310bf780b7dbb8d61c104042738';
    const CODE_FAKE_THB = 'eedf8d0dc501e2cce26552f19b6e0976';
    const CODE_FAKE_VND = 'fa318a996f36593f0c8ca67074c00c16';
    const CODE_FAKE_BRL = '03b097d0aa464130e0a761c034e435db';
    const CODE_FAKE_TRY = 'e74ad5e36bdb74c1267722beb8c3fb6a';
    const CODE_FAKE_PHP = '5ab035441699124787ffbadf3986c8e7';

    const CODE_FAKE_BTC = 'e589c717ba28f448b2583220bc68436b';
    const CODE_FAKE_BCH = '35b52853e090101fd85226dd70cf804d';
    const CODE_FAKE_TRX = 'c1c61cd37fb9ac2824cb0c020725a2ed';
    const CODE_FAKE_ETH = '45dffd22869a666e27d56fe553a7fe0d';
    const CODE_FAKE_XRP = '6ddaf34bdf2eab0bf4a909ee11d4e11c';
    const CODE_FAKE_LTC = '8986c456974abc3b6c892acd4bd2573e';
    const CODE_FAKE_USDT = '7add751c68947b86e82441abb95ca427';

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
        $precision = self::PRECISIONS_MAP[$currency] ?? self::PRECISIONS_MAP[self::CODE_USD];

        return (int) (
            (self::CORRECTION_FLOAT_VALUE + $balance) * pow(10, $precision)
        );
    }
}
<?php

namespace PrivateDev\Utils\Random;

class RandService
{
    /**
     * @var int
     */
    private static $generation = 1;

    /**
     * @param int $min
     * @param int $max
     *
     * @return float
     */
    public static function randFloat($min, $max)
    {
        list($usec, $sec) = explode(' ', microtime());
        mt_srand($sec * self::$generation + $usec * 1000000);

        self::$generation++;

        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public static function randInt($min, $max)
    {
        list($usec, $sec) = explode(' ', microtime());
        mt_srand($sec * self::$generation + $usec * 1000000);

        return mt_rand($min, $max);
    }
}
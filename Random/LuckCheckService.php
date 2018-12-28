<?php

namespace PrivateDev\Utils\Random;

class LuckCheckService
{
    /**
     * @param float $chance
     *
     * @return bool
     */
    public static function isLuck(float $chance)
    {
        return $chance > RandService::randFloat(0, 0.999);
    }
}
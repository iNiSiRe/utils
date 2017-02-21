<?php

namespace PrivateDev\Utils\Order;

class AbstractOrder implements OrderInterface
{
    /**
     * @return array
     */
    public function getOrder()
    {
        return get_object_vars($this);
    }
}

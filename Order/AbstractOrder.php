<?php

namespace PrivateDev\Utils\Order;

class AbstractOrder implements OrderInterface
{
    const ASC = 'ASC';

    const DESC = 'DESC';

    /**
     * @return array
     */
    public function getOrder()
    {
        return get_object_vars($this);
    }
}

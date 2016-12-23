<?php

namespace PrivateDev\Utils\Fractal;

class DataArraySerializer extends \League\Fractal\Serializer\DataArraySerializer
{
    public function null()
    {
        return [
            'data' => null,
        ];
    }
}
<?php

namespace PrivateDev\Utils\Fractal;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Serializer\SerializerAbstract;

class FractalBuilder
{
    /**
     * @var JsonApiSerializer
     */
    private $serializer;

    /**
     * FractalBuilder constructor.
     *
     * @param JsonApiSerializer $serializer
     */
    public function __construct(SerializerAbstract $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return Manager
     */
    public function build()
    {
        $fractal = new Manager();
        $fractal->setSerializer($this->serializer);

        return $fractal;
    }
}
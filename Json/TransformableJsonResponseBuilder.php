<?php

namespace PrivateDev\Utils\Json;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use PrivateDev\Utils\Fractal\TransformerAbstract;
use PrivateDev\Utils\Fractal\TranslatableTransformerInterface;

class TransformableJsonResponseBuilder extends JsonResponseBuilder
{
    /**
     * @var string
     */
    protected $fallbackLanguage;

    /**
     * TransformableJsonResponseBuilder constructor.
     *
     * @param Manager $fractal
     * @param string  $fallbackLanguage
     */
    public function __construct(Manager $fractal, string $fallbackLanguage)
    {
        parent::__construct($fractal);
        $this->fallbackLanguage = $fallbackLanguage;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return TransformableJsonResponseBuilder
     */
    private function setTransformableResource(ResourceInterface $resource)
    {
        $transformed = $this->fractal
            ->createData($resource)
            ->toArray();

        $this->setData('data', $transformed['data']);

        if (isset($transformed['included'])) {
            $this->setData('included', $transformed['included']);
        }

        return $this;
    }

    /**
     * @param $object
     * @param TransformerAbstract $transformer
     * 
     * @return TransformableJsonResponseBuilder
     */
    public function setTransformableItem($object, TransformerAbstract $transformer)
    {
        if ($transformer instanceof TranslatableTransformerInterface) {
            if (is_null($this->requestStack)) {
                throw new \Exception("You must set RequestSet in service definition for translatable entity");
            }
            $transformer->setLanguage((string) $this->requestStack->getCurrentRequest()->getPreferredLanguage());
            $transformer->setFallbackLanguage($this->fallbackLanguage);
        }

        return $this->setTransformableResource(new Item($object, $transformer, $transformer->getResourceKey()));
    }

    /**
     * @param $collection
     * @param TransformerAbstract $transformer
     * @return TransformableJsonResponseBuilder
     * @throws \Exception
     */
    public function setTransformableCollection($collection, TransformerAbstract $transformer)
    {
        if ($transformer instanceof TranslatableTransformerInterface) {
            if (is_null($this->requestStack)) {
                throw new \Exception("You must set RequestSet in service definition for translatable entity");
            }
            $transformer->setLanguage((string) $this->requestStack->getCurrentRequest()->getPreferredLanguage());
            $transformer->setFallbackLanguage($this->fallbackLanguage);
        }

        return $this->setTransformableResource(new Collection($collection, $transformer, $transformer->getResourceKey()));
    }
}
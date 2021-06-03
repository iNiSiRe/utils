<?php

namespace PrivateDev\Utils\ORM\Annotations;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Blendable implements Annotation
{
    /**
     * @var string
     */
    public $targetDocument;

    /**
     * @var string
     */
    public $joinColumnName;

    /**
     * @var string
     */
    public $referencedColumnName;
}
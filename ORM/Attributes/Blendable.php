<?php

namespace PrivateDev\Utils\ORM\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Blendable
{
    public function __construct(
        public string $targetDocument,
        public string $joinColumnName,
        public string $referencedColumnName,
    )
    {
    }
}

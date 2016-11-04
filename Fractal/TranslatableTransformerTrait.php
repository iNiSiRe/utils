<?php

namespace PrivateDev\Utils\Fractal;

use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * Class TranslationEntityTrait
 *
 * @package Utils\Entity
 */
trait TranslatableTransformerTrait
{
    protected $language;

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }
}
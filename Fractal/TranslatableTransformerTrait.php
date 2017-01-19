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
    /**
     * @var string
     */
    protected $language = '';

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    public function getLanguage() : string
    {
        return $this->language;
    }
}
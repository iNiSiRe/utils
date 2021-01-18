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

    /**
     * @var string
     */
    protected $fallbackLanguage;

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getFallbackLanguage()
    {
        return $this->fallbackLanguage;
    }

    /**
     * @param string $fallbackLanguage
     *
     * @return TranslatableTransformerTrait
     */
    public function setFallbackLanguage($fallbackLanguage)
    {
        $this->fallbackLanguage = $fallbackLanguage;

        return $this;
    }
}
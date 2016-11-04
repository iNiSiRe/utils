<?php

namespace PrivateDev\Utils\Entity;

use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * Class TranslationEntityTrait
 *
 * @package Utils\Entity
 */
trait TranslationEntityTrait
{
    /**
     * @var Translation
     *
     * @ORM\OneToOne(targetEntity="\PrivateDev\Utils\Entity\Translation", cascade={"all"}, fetch="EAGER")
     */
    private $translation;

    /**
     * @param mixed $translation
     * @return static
     */
    public function setTranslation(Translation $translation)
    {
        $this->translation = $translation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Set translation for field
     *
     * @param string $field
     * @param $value
     * @param string $language
     * @return static
     *
     *
     */
    protected function setTranslationForField(string $field, $value, string $language)
    {
        if (!$this->translation) {
            $this->translation = (new Translation())
                ->setEntityClass(static::class);
        }

        $this->translation->setFieldTranslation($field, $value, $language);

        return $this;
    }

    /**
     * Get translation for field
     *
     * @param $field
     * @param $language
     * @return string
     */
    protected function getTranslationForField(string $field, string $language)
    {
        try {
            $value = $this->translation ? $this->translation->getFieldTranslation($field, $language) : null;
        } catch (\Exception $e) {
            $value = null;
        }

        return $value;
    }
}
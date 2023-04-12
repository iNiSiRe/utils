<?php

namespace PrivateDev\Utils\Entity;

use Exception;
use Doctrine\ORM\Mapping as ORM;

trait TranslationEntityTrait
{
    #[ORM\OneToOne(targetEntity: Translation::class, cascade: ['all'], fetch: 'EAGER')]
    private ?Translation $translation = null;

    public function setTranslation(Translation $translation) : static
    {
        $this->translation = $translation;

        return $this;
    }

    public function getTranslation() : ?Translation
    {
        return $this->translation;
    }

    protected function setTranslationForField(string $field, $value, string $language) : static
    {
        if (!$this->translation) {
            $this->translation = (new Translation())
                ->setEntityClass(static::class);
        }

        $this->translation->setFieldTranslation($field, $value, $language);

        return $this;
    }

    protected function getTranslationForField(string $field, string $language) : ?string
    {
        try {
            $value = $this->translation?->getFieldTranslation($field, $language);
        } catch (Exception $e) {
            $value = null;
        }

        return $value;
    }
}
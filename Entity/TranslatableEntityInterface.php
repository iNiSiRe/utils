<?php

namespace PrivateDev\Utils\Entity;


interface TranslatableEntityInterface
{
    public function getTranslation();

    public function setTranslation(Translation $translation);

    public function getTranslatableFields();
}
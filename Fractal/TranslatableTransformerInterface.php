<?php
namespace PrivateDev\Utils\Fractal;


interface TranslatableTransformerInterface
{
    public function setLanguage(string $language);

    public function setFallbackLanguage(string $language);
}
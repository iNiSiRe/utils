<?php

namespace PrivateDev\Utils\Filter;

class PartialMatchText
{
    private $text;

    /**
     * @param mixed $text
     *
     * @return PartialMatchText
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }
}
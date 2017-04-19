<?php

namespace PrivateDev\Utils\Error;

interface ErrorInterface
{
    /**
     * @return string
     */
    public function getCode() : string ;

    /**
     * @return string
     */
    public function getOrigin() : string;

    /**
     * @return string
     */
    public function getMessage() : string;

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @return int|null
     */
    public function getPlural();

    /**
     * @return array
     */
    public function getParameters();
}
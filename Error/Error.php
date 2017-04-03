<?php

namespace PrivateDev\Utils\Error;

class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private $code;
    
    /**
     * @var string
     */
    private $origin;
    
    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var string
     */
    private $template;

    /**
     * @var null|int
     */
    private $plural;

    /**
     * Error constructor.
     *
     * @param int    $code
     * @param string $origin
     * @param string $message
     */
    public function __construct(string $message, string $template = "", array $parameters = [], $plural = null,
                                string $code = "", string $origin = "")
    {
        $this->message = $message;
        $this->template = $template;
        $this->parameters = $parameters;
        $this->plural = $plural;
        $this->code = $code;
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getOrigin() : string
    {
        return $this->origin;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return int|null
     */
    public function getPlural()
    {
        return $this->plural;
    }
}
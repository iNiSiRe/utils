<?php

namespace PrivateDev\Utils\Test;

class Request
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var bool
     */
    private $performAuth = false;

    /**
     * @var string
     */
    private $authToken = null;

    /**
     * Request constructor.
     *
     * @param string $url
     * @param string $method
     * @param array  $parameters
     */
    public function __construct(string $url, string $method = 'get', array $parameters = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Request
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return Request
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return Request
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPerformAuth()
    {
        return $this->performAuth;
    }

    /**
     * @param boolean $performAuth
     *
     * @return Request
     */
    public function setPerformAuth($performAuth)
    {
        $this->performAuth = $performAuth;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @param string $authToken
     *
     * @return Request
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;

        return $this;
    }
}
<?php

namespace PrivateDev\Utils\Json;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PrivateDev\Utils\Error\ErrorInterface;
use PrivateDev\Utils\Error\ErrorList;
use PrivateDev\Utils\Error\ErrorListInterface;
use PrivateDev\Utils\Form\FormErrorAdapter;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class JsonResponseBuilder
{
    /**
     * @var array
     */
    protected $body;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var JsonResponse
     */
    protected $response;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * JsonResponseBuilder constructor.
     *
     * @param Manager $fractal
     */
    public function __construct(Manager $fractal)
    {
        $this->body = [];
        $this->fractal = $fractal;
        $this->response = (new JsonResponse())->setEncodingOptions(JSON_INVALID_UTF8_IGNORE);
    }

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $name
     * @param mixed  $data
     *
     * @return $this
     */
    public function setData(string $name, $data)
    {
        $this->body[$name] = $data;

        return $this;
    }

    /**
     * @param ErrorInterface $error
     *
     * @return $this
     */
    public function addError(ErrorInterface $error)
    {
        $this->body['errors'][] = $this->formatError($error);

        return $this;
    }

    /**
     * @param ErrorListInterface $errorList
     *
     * @return JsonResponseBuilder
     */
    public function addErrorList(ErrorListInterface $errorList)
    {
        foreach ($errorList->all() as $error) {
            $this->addError($error);
        }

        return $this;
    }

    /**
     * @param ErrorInterface $error
     *
     * @return array
     */
    protected function formatError(ErrorInterface $error)
    {
        if ($error->getCode() != "") {
            $formatted['code'] = $error->getCode();
        }

        if ($error->getOrigin() != "") {
            $formatted['origin'] = $error->getOrigin();
        }

        if ($error->getTemplate() != "") {
            $formatted['template'] = $error->getTemplate();
            $formatted['plural'] = $error->getPlural();
            $formatted['parameters'] = $error->getParameters();
        }

        $formatted['message'] = $error->getMessage();

        return $formatted;
    }

    /**
     * @param int $statusCode
     *
     * @return JsonResponse
     */
    public function build(int $statusCode = 200)
    {
        $this->response->setStatusCode($statusCode);
        $this->response->setData($this->body);

        return $this->response;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setHeader(string $key, string $value)
    {
        $this->response->headers->set($key, $value);

        return $this;
    }
}
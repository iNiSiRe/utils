<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 28.06.16
 * Time: 19:49
 */

namespace PrivateDev\Utils\Error;

class ErrorList implements ErrorListInterface
{
    /**
     * @var ErrorInterface[]
     */
    private $errors = [];

    /**
     * @param ErrorInterface $error
     */
    public function add(ErrorInterface $error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return ErrorInterface[]
     */
    public function all() : array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->all() as $error) {
            $array[] = [
                'message' => $error->getMessage(),
                'template' => $error->getTemplate(),
                'plural' => $error->getPlural(),
                'parameters' => $error->getParameters(),
                'code' => $error->getCode(),
                'origin' =>$error->getOrigin()
            ];
        }

        return $array;
    }
}
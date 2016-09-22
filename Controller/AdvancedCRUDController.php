<?php

namespace PrivateDev\Utils\Controller;

use MWL\ClientBundle\Security\Guard\JsonWebTokenAuthenticator;
use PrivateDev\Utils\Error\ErrorCodes;
use PrivateDev\Utils\Form\FormErrorAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AdvancedCRUDController extends CRUDController
{
    /**
     * @return FormInterface
     */
    abstract function createFilterForm();

    /**
     * @return int
     */
    protected function getCollectionMaxSize() : int
    {
        return 100;
    }

    /**
     * @param array $filter
     *
     * @return array
     */
    protected function findByFilter(array $filter)
    {
        return $this->getEntityRepository()->findBy($filter, [], $this->getCollectionMaxSize());
    }

    /**
     * @Route()
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $filter = $this->createFilterForm();
        $filter->handleRequest($request);

        if ($filter->isValid()) {
            $entities = $this->findByFilter($filter->getData());
            $response = $this->getResponseBuilder()
                ->setTransformableCollection($entities, $this->createEntityTransformer())
                ->build();
        } else {
            $response = $this->getResponseBuilder()
                ->addErrorList(new FormErrorAdapter($filter->getErrors(true), ErrorCodes::VALIDATION_ERROR))
                ->build(JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
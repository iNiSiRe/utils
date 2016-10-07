<?php

namespace PrivateDev\Utils\Controller;

use Doctrine\ORM\QueryBuilder;
use PrivateDev\Utils\Error\ErrorCodes;
use PrivateDev\Utils\Filter\FilterInterface;
use PrivateDev\Utils\Filter\Query\FilterQueryComposer;
use PrivateDev\Utils\Form\FormErrorAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class CRUDLController extends CRUDController
{
    const ACTION_LIST = 5;

    /**
     * @return FormInterface
     */
    abstract protected function createFilterForm();

    /**
     * @return array
     */
    protected function getRoles()
    {
        return array_merge(parent::getRoles(), [
            self::ACTION_LIST => null
        ]);
    }

    const QUERY_BUILDER_ALIAS = 'e';

    /**
     * @return FilterQueryComposer
     */
    protected function getFilterQueryComposer()
    {
        return new FilterQueryComposer();
    }

    /**
     * @param FilterInterface $filter
     *
     * @return array
     */
    protected function findByFilter(FilterInterface $filter)
    {
        $builder = $this->getEntityRepository()->createQueryBuilder(self::QUERY_BUILDER_ALIAS);
        $this->getFilterQueryComposer()->compose($builder, self::QUERY_BUILDER_ALIAS, $filter);

        return $builder->getQuery()->getResult();
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
        $role = $this->getAccessRole(self::ACTION_LIST);

        if ($role && !$this->isGranted($role)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createFilterForm();
        $form->handleRequest($request);

        if ($form->isValid() || !$form->isSubmitted()) {

            $filter = $form->getData();
            $entities = $this->findByFilter($filter);

            $response = $this->getResponseBuilder()
                ->setTransformableCollection($entities, $this->createEntityTransformer())
                ->build();
        } else {
            $response = $this->getResponseBuilder()
                ->addErrorList(new FormErrorAdapter($form->getErrors(true), ErrorCodes::VALIDATION_ERROR))
                ->build(JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
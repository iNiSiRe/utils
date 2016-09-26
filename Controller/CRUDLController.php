<?php

namespace PrivateDev\Utils\Controller;

use Doctrine\ORM\QueryBuilder;
use PrivateDev\Utils\Error\ErrorCodes;
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

    /**
     * @return int
     */
    protected function getCollectionMaxSize() : int
    {
        return 100;
    }

    /**
     * @param QueryBuilder $builder
     * @param string       $alias
     * @param array        $filter
     */
    protected function buildFilterQuery(QueryBuilder $builder, string $alias, array $filter)
    {
        foreach ($filter as $key => $value) {

            if ($value === null) {
                continue;
            }

            $builder->andWhere("{$alias}.{$key} = :{$key}_value")
                ->setParameter("{$key}_value", $value);
        }

        $builder->setMaxResults($this->getCollectionMaxSize());
    }

    const QUERY_BUILDER_ALIAS = 'e';

    /**
     * @param array $filter
     *
     * @return array
     */
    protected function findByFilter(array $filter)
    {
        $builder = $this->getEntityRepository()->createQueryBuilder(self::QUERY_BUILDER_ALIAS);

        $this->buildFilterQuery($builder, self::QUERY_BUILDER_ALIAS, $filter);

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

        $filter = $this->createFilterForm();
        $filter->handleRequest($request);

        if ($filter->isValid() || !$filter->isSubmitted()) {

            $filterData = $filter->isSubmitted()
                ? $filter->getData()
                : [];

            $entities = $this->findByFilter($filterData);

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
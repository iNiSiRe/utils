<?php

namespace PrivateDev\Utils\Controller;

use Exception;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use PrivateDev\Utils\Builder\FilterQueryBuilder;
use PrivateDev\Utils\Builder\OrderQueryBuilder;
use PrivateDev\Utils\ORM\Blending\BlenderInterface;
use PrivateDev\Utils\Builder\PaginationQueryBuilder;
use PrivateDev\Utils\Filter as Filter;
use PrivateDev\Utils\Filter\Form\PaginationType;
use PrivateDev\Utils\Filter\Pagination;
use PrivateDev\Utils\Form\FormErrorAdapter;
use PrivateDev\Utils\Order\Form\EmptyOrderForm;
use PrivateDev\Utils\Order\EmptyOrder;
use PrivateDev\Utils\ORM\Paginator;
use PrivateDev\Utils\Permission\Permissions;
use PrivateDev\Utils\Builder\Query\QueryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class CRUDLController extends CRUDController
{
    const ACTION_LIST = 5;
    const PAGINATION_TOTAL_SIZE = 'X-Pagination-Size';

    /**
     * @param QueryInterface $query
     *
     * @return FormInterface
     */
    protected function createFilterForm(QueryInterface $query)
    {
        return $this->createForm(Filter\Form\EmptyFilterType::class, $query);
    }

    /**
     * @return QueryInterface
     */
    protected function createFilter()
    {
        return new Filter\EmptyFilter();
    }

    /**
     * @return Pagination
     */
    protected function createPagination()
    {
        return new Pagination();
    }

    /**
     * @param QueryInterface $query
     *
     * @return FormInterface
     */
    protected function createOrderForm(QueryInterface $query)
    {
        return $this->createForm(EmptyOrderForm::class, $query);
    }

    /**
     * @return QueryInterface
     */
    protected function createOrder()
    {
        return new EmptyOrder();
    }

    /**
     * @return array
     */
    protected function getRoles()
    {
        return array_merge(parent::getRoles(), [
            self::ACTION_LIST => Permissions::EMPTY
        ]);
    }

    /**
     * @param $builder
     *
     * @return FilterQueryBuilder
     */
    protected function getFilterQueryBuilder($builder)
    {
        return new FilterQueryBuilder($builder);
    }

    /**
     * @param $builder
     *
     * @return OrderQueryBuilder
     */
    protected function getOrderQueryBuilder($builder)
    {
        return new OrderQueryBuilder($builder);
    }

    /**
     * @param $builder
     *
     * @return PaginationQueryBuilder
     */
    protected function getPaginationQueryBuilder($builder)
    {
        return new PaginationQueryBuilder($builder);
    }

    /**
     * @return string
     */
    protected function getQueryEntityAlias()
    {
        return 'a';
    }

    /**
     * @return int
     */
    protected function getCollectionMaxSize() : int
    {
        return 100;
    }

    /**
     * @param $alias
     *
     * @return QueryBuilder
     */
    protected function createQueryBuilder($alias)
    {
        return $this->getEntityRepository()->createQueryBuilder($alias);
    }

    /**
     * @return BlenderInterface|null
     */
    protected function getDoctrineBlender()
    {
        return null;
    }

    /**
     * @param Request        $request
     * @param QueryInterface $filter
     * @param Pagination     $pagination
     * @param QueryInterface $order
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    protected function doList(Request $request, QueryInterface $filter, Pagination $pagination, QueryInterface $order)
    {
        $filterForm = $this->createFilterForm($filter);
        $paginationForm = $this->createForm(PaginationType::class, $pagination);
        $orderForm = $this->createOrderForm($order);

        $filterForm->handleRequest($request);
        $paginationForm->handleRequest($request);
        $orderForm->handleRequest($request);

        if (
            (!$filterForm->isSubmitted() || ($filterForm->isSubmitted() && $filterForm->isValid()))
            && (!$paginationForm->isSubmitted() || ($paginationForm->isSubmitted() && $paginationForm->isValid()))
            && (!$orderForm->isSubmitted() || ($orderForm->isSubmitted() && $orderForm->isValid()))
        ) {
            $alias = $this->getQueryEntityAlias();
            $builder = $this->createQueryBuilder($alias);

            $this->getFilterQueryBuilder($builder)->setQuery($filterForm->getData());
            $this->getOrderQueryBuilder($builder)->setQuery($orderForm->getData());

            // Statement 'select' collect
            $select[] = $alias;
            if (isset($builder->getDQLParts()['join'][$alias])) {
                /** @var Join $join */
                foreach ($builder->getDQLParts()['join'][$alias] as $join) {
                    if ($join->getCondition() == null && !in_array($join->getAlias(), $select)) {
                        $select[] = $join->getAlias();
                    }
                }
            }

            $builder->select(implode(',', $select));

            $responseBuilder = $this->getResponseBuilder();
            $paginationBuilder = $this->getPaginationQueryBuilder($builder)->setPagination($paginationForm->getData());

            $paginator = new Paginator($builder);
            $entities = $paginator->getIterator()->getArrayCopy();
            if ($this->isResponseIncludePagination()) {
                $responseBuilder->setHeader(self::PAGINATION_TOTAL_SIZE, $paginationBuilder->getTotalSize());
            }

            // Blending the ORM and MongoDB ODM
            $blender = $this->getDoctrineBlender();
            if ($blender) {
                $blender->blend($entities);
            }

            $responseBuilder->setTransformableCollection($entities, $this->createEntityTransformerForAction(self::ACTION_LIST));

            $response = $this->applyCacheOptions($responseBuilder->build());
        } else {
            $response = $this->getResponseBuilder()
                ->addErrorList(new FormErrorAdapter($filterForm->getErrors(true)))
                ->addErrorList(new FormErrorAdapter($orderForm->getErrors(true)))
                ->addErrorList(new FormErrorAdapter($paginationForm->getErrors(true)))
                ->build(JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    /**
     * @Route(methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function listAction(Request $request)
    {
        $this->checkAccess($request, self::ACTION_LIST);

        return $this->doList($request, $this->createFilter(), $this->createPagination(), $this->createOrder());
    }

    /**
     * @return boolean
     */
    protected function isResponseIncludePagination()
    {
        return false;
    }
}

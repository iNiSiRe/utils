<?php

namespace PrivateDev\Utils\Controller;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PrivateDev\Utils\Builder\FilterQueryBuilder;
use PrivateDev\Utils\Builder\OrderQueryBuilder;
use PrivateDev\Utils\Builder\PaginationQueryBuilder;
use PrivateDev\Utils\Filter as Filter;
use PrivateDev\Utils\Filter\Form\PaginationType;
use PrivateDev\Utils\Filter\Pagination;
use PrivateDev\Utils\Form\FormErrorAdapter;
use PrivateDev\Utils\Order\Form\EmptyOrderForm;
use PrivateDev\Utils\Order\EmptyOrder;
use PrivateDev\Utils\Permission\Permissions;
use PrivateDev\Utils\Builder\Query\QueryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    private function getFilterQueryBuilder($builder)
    {
        return new FilterQueryBuilder($builder);
    }

    /**
     * @param $builder
     *
     * @return OrderQueryBuilder
     */
    private function getOrderQueryBuilder($builder)
    {
        return new OrderQueryBuilder($builder);
    }

    /**
     * @param $builder
     *
     * @return PaginationQueryBuilder
     */
    private function getPaginationQueryBuilder($builder)
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
     * @param Request                        $request
     * @param QueryInterface $filter
     * @param Pagination                     $pagination
     * @param QueryInterface  $order
     *
     * @return JsonResponse
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
            ($filterForm->isValid() || !$filterForm->isSubmitted())
            && ($paginationForm->isValid() || !$paginationForm->isSubmitted())
            && ($orderForm->isValid() || !$orderForm->isSubmitted())
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
                    if ($join->getCondition() == null) {
                        $select[] = $join->getAlias();
                    }
                }
            }
            $builder->select(implode(',', $select));

            $responseBuilder = $this->getResponseBuilder();
            $paginationBuilder = $this->getPaginationQueryBuilder($builder)->setPagination($paginationForm->getData());

            $paginator = new Paginator($builder->getQuery());
            $entities = $paginator->getIterator()->getArrayCopy();
            if ($this->isResponseIncludePagination()) {
                $responseBuilder->setHeader(self::PAGINATION_TOTAL_SIZE, $paginationBuilder->getTotalSize());
            }

            $responseBuilder->setTransformableCollection($entities, $this->createEntityTransformer());

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
     * @Route()
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
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

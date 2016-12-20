<?php

namespace PrivateDev\Utils\Controller;

use PrivateDev\Utils\Error\ErrorCodes;
use PrivateDev\Utils\Filter as Filter;
use PrivateDev\Utils\Filter\Form\PaginationForm;
use PrivateDev\Utils\Filter\Model\FilterInterface;
use PrivateDev\Utils\Filter\Model\Pagination;
use PrivateDev\Utils\Form\FormErrorAdapter;
use PrivateDev\Utils\Permission\Permissions;
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
     * @param FilterInterface $filter
     *
     * @return FormInterface
     */
    protected function createFilterForm(FilterInterface $filter)
    {
        return $this->createForm(Filter\Form\EmptyFilterForm::class, $filter);
    }

    /**
     * @return FilterInterface
     */
    protected function createFilter()
    {
        return new Filter\Model\EmptyFilter();
    }

    /**
     * @return Pagination
     */
    protected function createPagination()
    {
        return new Pagination();
    }

    /**
     * @return array
     */
    protected function createOrder()
    {
        return [];
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

    const QUERY_BUILDER_ALIAS = 'e';

    /**
     * @return Filter\QueryBuilder
     */
    protected function getFilterQueryBuilder()
    {
        return new Filter\QueryBuilder($this->getEntityRepository());
    }

    /**
     * @param Request         $request
     * @param FilterInterface $filter
     * @param Pagination      $pagination
     *
     * @return JsonResponse
     */
    protected function doList(Request $request, FilterInterface $filter, Pagination $pagination)
    {
        $filterForm = $this->createFilterForm($filter);
        $paginationForm = $this->createForm(PaginationForm::class, $pagination);

        $filterForm->handleRequest($request);
        $paginationForm->handleRequest($request);

        if (
            ($filterForm->isValid() || !$filterForm->isSubmitted())
            && ($paginationForm->isValid() || !$paginationForm->isSubmitted())
        ) {
            $builder = $this->getFilterQueryBuilder()
                ->setFilter($filterForm->getData())
                ->setPagination($paginationForm->getData())
                ->setOrder($this->createOrder())
            ;

            $entities = $builder->getQuery()->getResult();

            $responseBuilder = $this->getResponseBuilder()
                ->setTransformableCollection($entities, $this->createEntityTransformer());

            if ($this->isResponseIncludePagination()) {
                $responseBuilder->setHeader(self::PAGINATION_TOTAL_SIZE, $builder->getTotalSize());
            }

            $response = $responseBuilder->build();
        } else {
            $response = $this->getResponseBuilder()
                ->addErrorList(new FormErrorAdapter($filterForm->getErrors(true), ErrorCodes::VALIDATION_ERROR))
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
        $role = $this->getAccessRole(self::ACTION_LIST);

        if ($role && !$this->isGranted($role)) {
            throw new AccessDeniedHttpException();
        }

        return $this->doList($request, $this->createFilter(), $this->createPagination());
    }

    /**
     * @return boolean
     */
    protected function isResponseIncludePagination()
    {
        return false;
    }
}
<?php

namespace PrivateDev\Utils\Controller;

use Doctrine\ORM\EntityRepository;
use PrivateDev\Utils\Entity\TranslatableEntityInterface;
use PrivateDev\Utils\Entity\Translation;
use PrivateDev\Utils\Form\FormErrorAdapter;
use PrivateDev\Utils\Fractal\TransformerAbstract;
use PrivateDev\Utils\Json\TransformableJsonResponseBuilder;
use PrivateDev\Utils\Permission\Permissions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class CRUDController extends Controller
{
    const ACTION_CREATE = 1;
    const ACTION_READ = 2;
    const ACTION_UPDATE = 3;
    const ACTION_DELETE = 4;

    /**
     * Get repository of the Entity
     *
     * @return EntityRepository
     */
    abstract protected function getEntityRepository();

    /**
     * Create Form for the Entity
     *
     * @param object $entity
     * @param array  $options
     *
     * @return FormInterface
     */
    abstract protected function createEntityForm($entity, array $options = []) : FormInterface;

    /**
     * Create transformer for the Entity
     *
     * @return TransformerAbstract
     */
    abstract protected function createEntityTransformer();

    /**
     * Create an empty Entity
     *
     * @return object
     */
    abstract protected function createEntity();

    /**
     * @return TransformableJsonResponseBuilder
     */
    abstract protected function getResponseBuilder();


    /**
     * Roles for actions
     *
     * Note: null - no restrictions
     *       true - action restricted
     *
     * @return array
     */
    protected function getRoles()
    {
        return [
            self::ACTION_CREATE => Permissions::EMPTY,
            self::ACTION_READ   => Permissions::EMPTY,
            self::ACTION_UPDATE => Permissions::EMPTY,
            self::ACTION_DELETE => Permissions::EMPTY
        ];
    }

    /**
     * @param int $action
     *
     * @return string|null
     */
    protected function getAccessRole(int $action)
    {
        $roles = $this->getRoles();

        return isset($roles[$action])
            ? $roles[$action]
            : null;
    }

    /**
     * @param $entity
     */
    protected function save($entity)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param $entity
     */
    protected function onCreateSuccess($entity)
    {
        $this->save($entity);
    }

    /**
     * @param $entity
     */
    protected function onUpdateSuccess($entity)
    {
        $this->save($entity);
    }

    /**
     * @param object  $entity
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function doUpdate($entity, Request $request)
    {
        $this->postEntityLoadCheckAccess(self::ACTION_UPDATE, $entity);

        $responseBuilder = $this->getResponseBuilder();

        $form = $this->createEntityForm($entity, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity = $form->getData();

            if ($entity instanceof TranslatableEntityInterface) {
                $language = $request->getPreferredLanguage();
                
                $translation = $entity->getTranslation() ? $entity->getTranslation() : new Translation();
                $translation->setEntityClass(get_class($entity));

                $propertyAccessor = new PropertyAccessor();
                foreach ($entity->getTranslatableFields() as $field) {
                    $translation->setFieldTranslation($field, $propertyAccessor->getValue($entity, $field), $language);
                }

                $entity->setTranslation($translation);
            }

            if ($entity->getId() == null) {
                $payload = $this->onCreateSuccess($entity);
            } else {
                $payload = $this->onUpdateSuccess($entity);
            }

            $responseBuilder->setTransformableItem($entity, $this->createEntityTransformer());

            if ($payload !== null) {
                $responseBuilder->setData('payload', $payload);
            }

            $response = $responseBuilder->build();
        } else {
            $response = $responseBuilder
                ->addErrorList(new FormErrorAdapter($form->getErrors(true)))
                ->build(JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    /**
     * @param int    $action
     * @param object $entity
     */
    protected function postEntityLoadCheckAccess($action, $entity)
    {
        // By default do nothing, but you can override it
    }

    /**
     * @Route()
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $role = $this->getAccessRole(self::ACTION_CREATE);

        if ($role && !$this->isGranted($role)) {
            throw new AccessDeniedHttpException();
        }

        $entity = $this->createEntity();

        return $this->doUpdate($entity, $request);
    }

    /**
     * @return array
     */
    protected function getCacheOptions()
    {
        return [];
    }

    /**
     * @return Response
     */
    protected function applyCacheOptions(Response $response)
    {
        return $response->setCache($this->getCacheOptions());
    }

    /**
     * @param $entity
     *
     * @return JsonResponse
     */
    protected function doRead($entity)
    {
        $this->postEntityLoadCheckAccess(self::ACTION_READ, $entity);

        $response = $this->getResponseBuilder()
            ->setTransformableItem($entity, $this->createEntityTransformer())
            ->build();

        return $this->applyCacheOptions($response);
    }

    /**
     * @Route(path="/{id}")
     * @Method({"GET"})
     *
     * @param $id
     *
     * @return Response
     */
    public function readAction($id)
    {
        $role = $this->getAccessRole(self::ACTION_READ);

        if ($role && !$this->isGranted($role)) {
            throw new AccessDeniedHttpException();
        }

        $entity = $this->getEntityRepository()->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->doRead($entity);
    }

    /**
     * @Route(path="/{id}")
     * @Method({"PUT", "PATCH"})
     *
     * @param Request $request
     * @param int     $id
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $id)
    {
        $role = $this->getAccessRole(self::ACTION_UPDATE);

        if ($role && !$this->isGranted($role)) {
            throw new AccessDeniedHttpException();
        }

        $entity = $this->getEntityRepository()->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->doUpdate($entity, $request);
    }

    /**
     * @param $entity
     *
     * @return JsonResponse
     */
    protected function doDelete($entity)
    {
        $this->postEntityLoadCheckAccess(self::ACTION_DELETE, $entity);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->remove($entity);
        $em->flush($entity);

        return $this
            ->getResponseBuilder()
            ->build();
    }

    /**
     * @Route("/{id}")
     * @Method({"DELETE"})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        $role = $this->getAccessRole(self::ACTION_DELETE);

        if ($role && !$this->isGranted($role)) {
            throw new AccessDeniedHttpException();
        }

        $entity = $this->getEntityRepository()->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->doDelete($entity);
    }
}
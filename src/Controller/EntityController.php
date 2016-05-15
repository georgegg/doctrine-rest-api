<?php
namespace pmill\Doctrine\Rest\Controller;

use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Rest\Traits\EntityHelperTrait;
use pmill\Doctrine\Rest\Traits\RequestHelperTrait;
use Symfony\Component\HttpFoundation\Request;

class EntityController
{
    use EntityHelperTrait;
    use RequestHelperTrait;

    /**
     * @param EntityManager $entityManager
     * @param Request $request
     */
    public function __construct(EntityManager $entityManager, Request $request)
    {
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    /**
     * @param $entityClass
     * @param $id
     * @return string
     */
    public function getAction($entityClass, $id)
    {
        return $this->findEntityById($entityClass, $id);
    }

    /**
     * @param $entityClass
     * @param $id
     * @return mixed
     * @throws \pmill\Doctrine\Rest\Exception\NotFoundException
     */
    public function patchAction($entityClass, $id)
    {
        $data = $this->getRequestPayload();

        $entity = $this->findEntityById($entityClass, $id);
        $entity = $this->hydrateEntity($entity, $data);

        return $this->persistEntity($entity);
    }

    /**
     * @param $entityClass
     * @param $id
     * @return array
     * @throws \pmill\Doctrine\Rest\Exception\NotFoundException
     */
    public function deleteAction($entityClass, $id)
    {
        $entity = $this->findEntityById($entityClass, $id);
        $this->removeEntity($entity);

        return ['success' => true];
    }
}

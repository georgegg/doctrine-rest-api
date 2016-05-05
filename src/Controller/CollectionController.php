<?php
namespace pmill\Doctrine\Rest\Controller;

use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Rest\Traits\EntityManagerHelperTrait;
use pmill\Doctrine\Rest\Traits\RequestHelperTrait;
use Symfony\Component\HttpFoundation\Request;

class CollectionController
{
    use EntityManagerHelperTrait;
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
     * @return array|object[]
     */
    public function getAction($entityClass)
    {
        return $this->findEntitiesBy($entityClass);
    }

    /**
     * @param $entityClass
     * @return object
     */
    public function postAction($entityClass)
    {
        $data = $this->getRequestPayload();
        $entity = $this->hydrateEntity($entityClass, $data);
        return $this->persistEntity($entity);
    }
}

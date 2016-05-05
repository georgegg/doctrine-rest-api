<?php
namespace pmill\Doctrine\Rest\Controller;

use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Rest\Traits\EntityManagerHelperTrait;

class EntityController
{
    use EntityManagerHelperTrait;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
}

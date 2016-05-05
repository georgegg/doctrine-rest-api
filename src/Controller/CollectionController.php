<?php
namespace pmill\Doctrine\Rest\Controller;

use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Rest\Traits\EntityManagerHelperTrait;

class CollectionController
{
    use EntityManagerHelperTrait;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $entityClass
     * @return string
     */
    public function getAction($entityClass)
    {
        return $this->findEntitiesBy($entityClass);
    }
}

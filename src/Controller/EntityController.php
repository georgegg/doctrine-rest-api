<?php
namespace pmill\Doctrine\Rest\Controller;

use Doctrine\ORM\EntityManager;

class EntityController
{
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
     * @param $id
     * @return string
     */
    public function getAction($entityClass, $id)
    {
        return [
            'entityClass' => $entityClass,
            'id' => $id,
        ];
    }
}

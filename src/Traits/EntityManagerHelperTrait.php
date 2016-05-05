<?php
namespace pmill\Doctrine\Rest\Traits;

use Doctrine\ORM\EntityNotFoundException;

trait EntityManagerHelperTrait
{
    /**
     * @param $entityClassName
     * @param $id
     * @return mixed
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    protected function findEntityById($entityClassName, $id)
    {
        if (!property_exists($this, 'entityManager')) {
            throw new \Exception('Cannot use EntityManagerHelperTrait in a class that does not declare an $entityManager property');
        }

        $entity = $this->entityManager->find($entityClassName, $id);
        if (is_null($entity)) {
            throw EntityNotFoundException::fromClassNameAndIdentifier($entityClassName, $id);
        }

        return $entity;
    }
}
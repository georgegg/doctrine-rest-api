<?php
namespace pmill\Doctrine\Rest\Traits;

use Doctrine\ORM\EntityNotFoundException;
use pmill\Doctrine\Rest\Exception\NotFoundException;
use pmill\Doctrine\Rest\Exception\RestException;

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
            throw new RestException('Cannot use EntityManagerHelperTrait in a class that does not declare an $entityManager property', 500);
        }

        $entity = $this->entityManager->find($entityClassName, $id);
        if (is_null($entity)) {
            throw new NotFoundException('We could not find a resource with the given id');
        }

        return $entity;
    }
}
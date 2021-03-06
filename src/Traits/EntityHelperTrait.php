<?php
namespace pmill\Doctrine\Rest\Traits;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use pmill\Doctrine\Rest\Exception\NotFoundException;
use pmill\Doctrine\Rest\Exception\RestException;

trait EntityHelperTrait
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param $entityClassName
     * @param $id
     * @return mixed
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    protected function findEntityById($entityClassName, $id)
    {
        $this->assertEntityManager();

        $entity = $this->entityManager->find($entityClassName, $id);
        if (is_null($entity)) {
            throw new NotFoundException('We could not find a resource with the given id');
        }

        return $entity;
    }

    /**
     * @param $entityClassName
     * @param array $criteria
     * @return array
     * @throws RestException
     */
    protected function findEntitiesBy($entityClassName, array $criteria = [])
    {
        $this->assertEntityManager();

        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository($entityClassName);
        return $repository->findBy($criteria);
    }

    /**
     * @param $entity
     * @param array $data
     * @return mixed|object
     * @throws \Exception
     */
    protected function hydrateEntity($entity, array $data)
    {
        $this->assertEntityManager();

        $hydrator = new ArrayHydrator($this->entityManager);
        return $hydrator->hydrate($entity, $data);
    }

    /**
     * @param $entity
     * @return mixed
     */
    protected function persistEntity($entity)
    {
        $this->assertEntityManager();

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param $entity
     * @throws RestException
     */
    protected function removeEntity($entity)
    {
        $this->assertEntityManager();

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @throws RestException
     */
    protected function assertEntityManager()
    {
        if (!$this->entityManager instanceof EntityManager) {
            throw new RestException('Cannot use EntityManagerHelperTrait in a class that does not declare an $entityManager property', 500);
        }
    }
}
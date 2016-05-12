<?php
namespace pmill\Doctrine\Rest;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use pmill\Doctrine\Rest\Event\Listener\PostUpdateValueListener;
use pmill\Doctrine\Rest\Event\Listener\PreUpdateValueListener;

class Doctrine
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var array
     */
    protected $entityClasses = [];

    /**
     * @param array $databaseConfig
     * @throws \Doctrine\ORM\ORMException
     */
    public function __construct(array $databaseConfig)
    {
        $doctrineConfig = Setup::createAnnotationMetadataConfiguration($databaseConfig['entityPath'], true, null, null, false);
        $doctrineConfig->setAutoGenerateProxyClasses(false);

        $eventManager = $this->createEventManager();
        $this->entityManager = EntityManager::create($databaseConfig, $doctrineConfig, $eventManager);
        $this->annotationReader = $this->getReaderFromEntityManager();
        $this->entityClasses = $this->getEntityClassesFromEntityManager();
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }

    /**
     * @return array
     */
    public function getEntityClasses()
    {
        return $this->entityClasses;
    }

    /**
     * @return AnnotationReader
     */
    protected function getReaderFromEntityManager()
    {
        $configuration = $this->entityManager->getConfiguration();
        /** @var AnnotationDriver $driver */
        $driver = $configuration->getMetadataDriverImpl();
        return $driver->getReader();
    }

    /**
     * @return array
     */
    protected function getEntityClassesFromEntityManager()
    {
        $entityClasses = [];

        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($metaData as $classMetaData) {
            $entityClasses[] = $classMetaData->getName();
        }

        return $entityClasses;
    }

    /**
     * @return EventManager
     */
    protected function createEventManager()
    {
        $eventManager = new EventManager();

        $preUpdateEventListener = new PreUpdateValueListener();
        $eventManager->addEventListener(Events::preUpdate, $preUpdateEventListener);

        $postUpdateEventListener = new PostUpdateValueListener();
        $eventManager->addEventSubscriber($postUpdateEventListener);

        return $eventManager;
    }
}
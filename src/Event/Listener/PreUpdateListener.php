<?php
namespace pmill\Doctrine\Rest\Event\Listener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use pmill\Doctrine\Rest\Annotation\PreUpdate;
use pmill\Doctrine\Rest\Event\Handler\HandlerInterface;

class PreUpdateListener
{
    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $annotationReader = $this->getReaderFromEntityManager($args->getEntityManager());
        $entity = $args->getEntity();
        $changeSet = $args->getEntityChangeSet();
        $reflObject = new \ReflectionObject($entity);

        foreach ($changeSet as $propertyName => $values) {
            $property = $reflObject->getProperty($propertyName);
            /** @var PreUpdate $annotation */
            if ($annotation = $annotationReader->getPropertyAnnotation($property, PreUpdate::class)) {
                $className = $annotation->value;

                if (is_subclass_of($className, HandlerInterface::class)) {
                    /** @var HandlerInterface $propertySubscriber */
                    $propertySubscriber = new $className();
                    $propertySubscriber->handle($entity, $propertyName, $args);
                }
            }
        }
    }

    /**
     * @param EntityManager $entityManager
     * @return AnnotationReader
     */
    protected function getReaderFromEntityManager(EntityManager $entityManager)
    {
        $configuration = $entityManager->getConfiguration();
        /** @var AnnotationDriver $driver */
        $driver = $configuration->getMetadataDriverImpl();
        return $driver->getReader();
    }
}

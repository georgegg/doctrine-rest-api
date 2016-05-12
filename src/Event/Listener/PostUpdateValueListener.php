<?php
namespace pmill\Doctrine\Rest\Event\Listener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use pmill\Doctrine\Rest\Annotation\PostUpdate;
use pmill\Doctrine\Rest\Event\Handler\HandlerInterface;

class PostUpdateValueListener implements EventSubscriber
{
    /**
     * @var array
     */
    protected $changedProperties = [];

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postUpdate,
        ];
    }

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

            /** @var PostUpdate $annotation */
            if ($annotation = $annotationReader->getPropertyAnnotation($property, PostUpdate::class)) {
                $className = $annotation->value;

                if (is_subclass_of($className, HandlerInterface::class)) {
                    $this->changedProperties[] = [
                        'handlerClassName' => $className,
                        'entity' => $entity,
                        'propertyName' => $propertyName,
                        'args' => $args,
                    ];
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        foreach ($this->changedProperties as $property) {
            list($className, $entity, $propertyName, $args) = array_values($property);

            /** @var HandlerInterface $propertySubscriber */
            $propertySubscriber = new $className();
            $propertySubscriber->handle($entity, $propertyName, $args);
        }

        $this->changedProperties = [];
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

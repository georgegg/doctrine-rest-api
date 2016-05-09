<?php
namespace pmill\Doctrine\Rest\Event\Subscriber;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use pmill\Doctrine\Rest\Annotation\PreUpdate;
use pmill\Doctrine\Rest\Event\Handler\HandlerInterface;

class PreUpdateSubscriber implements EventSubscriber
{
    /**
     * @var Reader
     */
    protected $annotationReader;

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['preUpdate'];
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $changeSet = $args->getEntityChangeSet();
        $reflObject = new \ReflectionObject($entity);

        foreach ($changeSet as $propertyName => $values) {
            $property = $reflObject->getProperty($propertyName);
            /** @var PreUpdate $annotation */A
            if ($annotation = $this->annotationReader->getPropertyAnnotation($property, PreUpdate::class)) {
                $className = $annotation->value;
                if ($className instanceof HandlerInterface) {
                    /** @var HandlerInterface $propertySubscriber */
                    $propertySubscriber = new $className();
                    $propertySubscriber->handle($entity, $propertyName, $args);
                }
            }
        }
    }
}

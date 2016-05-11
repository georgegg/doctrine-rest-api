<?php
namespace pmill\Doctrine\Rest\Event\Handler;

class HashValue implements HandlerInterface
{
    /**
     * @param $object
     * @param $property
     * @param $args
     */
    public function handle(&$object, $property, $args)
    {
        $reflObject = new \ReflectionObject($object);
        $property = $reflObject->getProperty($property);
        $property->setAccessible(true);

        $hashedPassword = $this->getHashedPassword($property->getValue($object));
        $property->setValue($object, $hashedPassword);
    }

    /**
     * @param $value
     * @return bool|string
     */
    protected function getHashedPassword($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}

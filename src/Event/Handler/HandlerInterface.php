<?php
namespace pmill\Doctrine\Rest\Event\Handler;

interface HandlerInterface
{
    /**
     * @param $object
     * @param $property
     * @param $args
     */
    public function handle(&$object, $property, $args);
}

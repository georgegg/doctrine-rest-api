<?php
namespace pmill\Doctrine\Rest;

class Container
{
    /**
     * @var \DI\Container
     */
    protected $phpDiContainer;

    public function __construct()
    {
        $this->phpDiContainer = \DI\ContainerBuilder::buildDevContainer();
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->phpDiContainer->set($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \DI\NotFoundException
     */
    public function get($name)
    {
        return $this->phpDiContainer->get($name);
    }

    /**
     * @param $class
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function call($class, $method, $params = [])
    {
        return $this->phpDiContainer->call([$class, $method], $params);
    }

    /**
     * @param $class
     * @param array $params
     * @return mixed
     * @throws \DI\NotFoundException
     */
    public function make($class, array $params = [])
    {
        return $this->phpDiContainer->make($class, $params);
    }
}

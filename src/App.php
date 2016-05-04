<?php
namespace pmill\Doctrine\Rest;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Noodlehaus\Config;
use Symfony\Component\HttpFoundation\Request;

class App
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Doctrine
     */
    protected $doctrine;

    /**
     * @param $configDirectory
     */
    public function __construct($autoloader, $configDirectory)
    {
        AnnotationRegistry::registerLoader(array($autoloader, 'loadClass'));

        $this->config = new Config($configDirectory);
        $this->doctrine = new Doctrine($this->config->get('database'));
        $this->router = new Router($this->doctrine);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function run(Request $request = null)
    {
        return $this->router->match($request);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return Doctrine
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param Doctrine $doctrine
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }
}

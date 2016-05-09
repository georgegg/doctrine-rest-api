<?php
namespace pmill\Doctrine\Rest;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Noodlehaus\Config;
use pmill\Doctrine\Rest\Exception\RestException;
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
     * @var Container
     */
    protected $container;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param $autoloader
     * @param $configDirectory
     */
    public function __construct($autoloader, $configDirectory)
    {
        AnnotationRegistry::registerLoader([$autoloader, 'loadClass']);

        $this->config = new Config($configDirectory);
        $this->doctrine = new Doctrine($this->config->get('database'));
        $this->container = $this->setupContainer();

        $this->router = new Router($this->doctrine);
        $this->dispatcher = new Dispatcher($this->container);
        $this->response = new Response($this->doctrine->getAnnotationReader());
    }

    /**
     * @param Request $request
     * @return array|\Exception|mixed|RestException
     */
    public function handle(Request $request = null)
    {
        $this->container->set(Request::class, $request);

        try {
            $routeData = $this->router->match($request);
            $routeResult = $this->dispatcher->dispatchRoute($routeData);
        } catch (RestException $e) {
            $routeResult = $e;
        } catch (\Exception $e) {
            $routeResult = [
                'error' => 'An unexpected error has occurred',
                'error_detail' => $e->getMessage(),
                'code' => 500,
            ];
        }

        return $this->response->handle($routeResult);
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

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return Container
     */
    protected function setupContainer()
    {
        $container = new Container();
        $container->set(EntityManager::class, $this->doctrine->getEntityManager());

        return $container;
    }
}

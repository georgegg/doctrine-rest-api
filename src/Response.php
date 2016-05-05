<?php
namespace pmill\Doctrine\Rest;

use Doctrine\Common\Annotations\Reader;
use League\Fractal\Manager;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use pmill\Doctrine\Rest\Annotation\FractalTransformer;
use pmill\Doctrine\Rest\Exception\RestException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response
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
     * @param $result
     * @return SymfonyResponse
     */
    public function handle($result)
    {
        if ($result instanceof RestException) {
            return $this->createJsonResponse($result, $result->getCode());
        }

        if (is_object($result)) {
            $entityClass = get_class($result);

            if ($transformer = $this->getEntityTransformer($entityClass)) {
                $resource = new FractalItem($result, $transformer);
                return $this->createFractalResponse($resource);
            }
        }

        return $this->createJsonResponse($result);
    }

    /**
     * @param ResourceAbstract $resource
     * @return SymfonyResponse
     */
    protected function createFractalResponse(ResourceAbstract $resource)
    {
        $fractal = new Manager();
        $fractal->setSerializer(new ArraySerializer());

        $data = $fractal->createData($resource)->toArray();
        return $this->createJsonResponse($data);
    }

    /**
     * @param $payload
     * @param int $statusCode
     * @return SymfonyResponse
     */
    protected function createJsonResponse($payload, $statusCode = 200)
    {
        return new SymfonyResponse(json_encode($payload), $statusCode, [
                'Content-Type' => 'application/json',
            ]
        );
    }

    /**
     * @param $entityClass
     * @return TransformerAbstract|null
     */
    protected function getEntityTransformer($entityClass)
    {
        $reflectionClass = new \ReflectionClass($entityClass);

        /** @var FractalTransformer $annotation */
        $annotation = $this->annotationReader->getClassAnnotation($reflectionClass, FractalTransformer::class);
        if (is_null($annotation)) {
            return null;
        }

        $transformerClass = $annotation->value;
        return new $transformerClass();
    }
}

<?php
namespace pmill\Doctrine\Rest;

use pmill\Doctrine\Rest\Exception\RestException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response
{
    /**
     * @param $result
     * @return SymfonyResponse
     */
    public function handle($result)
    {
        if ($result instanceof RestException) {
            return $this->createJsonResponse($result, $result->getCode());
        }

        return $this->createJsonResponse($result);
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
}

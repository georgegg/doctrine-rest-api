<?php
namespace pmill\Doctrine\Rest\Middleware;

use pmill\Doctrine\Rest\Exception\AuthenticationException;
use pmill\Doctrine\Rest\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param Request $request
     * @throws AuthenticationException
     */
    public function handle(Request &$request)
    {
        $tokenHeader = $request->headers->get('Authorization');
        preg_match("/Bearer:? (.*)/", $tokenHeader, $output_array);

        if (count($tokenHeader) != 2) {
            throw new AuthenticationException('No authentication token provided', 401);
        }

        $this->authenticationService->authenticateWithToken($tokenHeader[1]);
    }
}

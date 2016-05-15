<?php
namespace pmill\Doctrine\Rest\Example\Controller;

use pmill\Doctrine\Rest\Annotation as RPC;
use pmill\Doctrine\Rest\Example\Entity\User;
use pmill\Doctrine\Rest\Exception\AuthenticationException;
use pmill\Doctrine\Rest\Service\AuthenticationService;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;
use Symfony\Component\HttpFoundation\Request;

/**
 * @REST\Controller
 */
class AuthenticationController
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     * @param AuthenticationService $authenticationService
     */
    public function __construct(Request $request, AuthenticationService $authenticationService)
    {
        $this->request = $request;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @RPC\Route("/api/auth/login")
     * @RPC\Method("POST")
     */
    public function login()
    {
        $credentials = json_decode($this->request->getContent(), true);

        try {
            v::create()
                ->key('email', v::notEmpty())
                ->key('password', v::notEmpty())
                ->assert($credentials);
        } catch (ValidationException $e) {
            $errors = $e->findMessages(['email', 'password']);
            throw new \pmill\Doctrine\Rest\Exception\ValidationException($errors);
        }

        $password = $credentials['password'];
        unset($credentials['password']);

        /** @var User $user */
        $user = $this->authenticationService->authenticateWithCredentials(User::class, $credentials, $password);
        $token = $this->authenticationService->generateTokenFromObject($user);
        return ['token' => $token];
    }
}

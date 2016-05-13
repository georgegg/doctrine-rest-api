<?php
namespace pmill\Doctrine\Rest\Example\Controller;

use pmill\Doctrine\Rest\Service\AuthenticationService;

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
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @RPC\Url("/api/auth/login")
     * @RPC\Method("POST")
     */
    public function login()
    {

    }
}
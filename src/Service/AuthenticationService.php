<?php
namespace pmill\Doctrine\Rest\Service;

use Doctrine\ORM\EntityManager;
use Firebase\JWT\JWT;
use Noodlehaus\Config;

class AuthenticationService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $jwtConfig;

    /**
     * @param EntityManager $entityManager
     * @param Config $config
     */
    public function __construct(EntityManager $entityManager, Config $config)
    {
        $this->entityManager = $entityManager;
        $this->jwtConfig = $config->get('authentication.jwt');
    }

    public function generateToken()
    {

    }

    public function authenticateWithToken($token)
    {
        $payload = JWT::decode($token, $this->jwtConfig['key'], $this->jwtConfig['algorithms']);

    }
}

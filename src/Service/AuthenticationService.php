<?php
namespace pmill\Doctrine\Rest\Service;

use Doctrine\ORM\EntityManager;
use Firebase\JWT\JWT;
use Noodlehaus\Config;
use pmill\Doctrine\Rest\AuthenticatableWithToken;
use pmill\Doctrine\Rest\Exception\AuthenticationException;

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
     * @var object
     */
    protected $loggedInEntity;

    /**
     * @param EntityManager $entityManager
     * @param Config $config
     */
    public function __construct(EntityManager $entityManager, Config $config)
    {
        $this->entityManager = $entityManager;
        $this->jwtConfig = $config->get('authentication.jwt');
    }

    /**
     * @param AuthenticatableWithToken $object
     * @return string
     */
    public function generateTokenFromObject(AuthenticatableWithToken $object)
    {
        $token = [
            "iss" => $this->jwtConfig['issuer'],
            "aud" => $this->jwtConfig['subject'],
            "iat" => time(),
            "nbf" => strtotime($this->jwtConfig['expires']),
            "object" => [
                "entity" => get_class($object),
                "id" => $object->getTokenIdentifier(),
            ],
        ];

        return JWT::encode($token, $this->jwtConfig['key']);
    }

    /**
     * @return string
     */
    public function generateTokenFromLoggedInEntity()
    {
        return $this->generateTokenFromObject($this->loggedInEntity);
    }

    /**
     * @param $token
     * @return array
     * @throws AuthenticationException
     */
    public function authenticateWithToken($token)
    {
        $payload = JWT::decode($token, $this->jwtConfig['key'], $this->jwtConfig['algorithms']);

        $objectRepository = $this->entityManager->getRepository($payload['object']['entity']);
        $object = $objectRepository->findBy($payload['object']['id']);
        if (is_null($object)) {
            throw new AuthenticationException('Authentication by token failed', 401);
        }

        return $this->loggedInEntity = $object;
    }

    /**
     * @param $entityClass
     * @param array $credentials
     * @return array
     * @throws AuthenticationException
     */
    public function authenticateWithCredentials($entityClass, array $credentials)
    {
        $objectRepository = $this->entityManager->getRepository($entityClass);
        $object = $objectRepository->findBy($credentials);
        if (is_null($object)) {
            throw new AuthenticationException('Authentication by credentials failed', 401);
        }

        return $this->loggedInEntity = $object;
    }
}

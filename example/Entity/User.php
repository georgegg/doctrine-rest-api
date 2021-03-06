<?php
namespace pmill\Doctrine\Rest\Example\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use pmill\Doctrine\Rest\Annotation as REST;
use pmill\Doctrine\Rest\AuthenticatableWithToken;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @REST\Route(entity="/api/user/{id}", collection="/api/user")
 * @REST\FractalTransformer("pmill\Doctrine\Rest\Example\Transformer\UserTransformer")
 * @REST\MethodAuthentication({"PATCH"})
 */
class User implements JsonSerializable, AuthenticatableWithToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     * @REST\PreUpdate("pmill\Doctrine\Rest\Event\Handler\HashValue")
     * @var string
     */
    protected $password;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * @return array
     */
    public function getTokenIdentifier()
    {
        return [
            'id' => $this->id,
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}

<?php
namespace pmill\Doctrine\Rest\Example\Transformer;

use League\Fractal\TransformerAbstract;
use pmill\Doctrine\Rest\Example\Entity\User;

class UserTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password_hash' => $user->getPassword(),
        ];
    }
}

<?php
namespace pmill\Doctrine\Rest\AuthenticatableWithToken;

interface AuthenticatableWithToken
{
    /**
     * @return array
     */
    public function getTokenIdentifier();
}

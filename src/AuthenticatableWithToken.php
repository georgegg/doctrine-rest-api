<?php
namespace pmill\Doctrine\Rest;

interface AuthenticatableWithToken
{
    /**
     * @return array
     */
    public function getTokenIdentifier();
}

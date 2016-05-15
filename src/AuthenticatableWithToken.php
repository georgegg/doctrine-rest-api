<?php
namespace pmill\Doctrine\Rest;

interface AuthenticatableWithToken
{
    /**
     * @return array
     */
    public function getTokenIdentifier();

    /**
     * @return string
     */
    public function getPassword();
}

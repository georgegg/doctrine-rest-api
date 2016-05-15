<?php
namespace pmill\Doctrine\Rest\Exception;

class AuthenticationException extends RestException
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = 401)
    {
        parent::__construct($message, $code, null);
    }
}

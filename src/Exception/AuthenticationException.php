<?php
namespace pmill\Doctrine\Rest\Exception;

class AuthenticationException extends RestException
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message, $code, null);
    }
}

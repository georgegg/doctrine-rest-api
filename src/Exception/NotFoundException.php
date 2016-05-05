<?php
namespace pmill\Doctrine\Rest\Exception;

class NotFoundException extends RestException
{
    /**
     * @param $entityClassName
     * @param $id
     * @return NotFoundException
     */
    public static function fromClassNameAndIdentifier($entityClassName, $id)
    {
        $message = sprintf('Entity of type \'%s\' for ID %d was not found', $entityClassName, $id);
        return new self($message, 404);
    }

    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message, $code, null);
    }
}
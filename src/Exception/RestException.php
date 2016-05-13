<?php
namespace pmill\Doctrine\Rest\Exception;

use Exception;
use JsonSerializable;

class RestException extends Exception implements JsonSerializable
{
    /**
     * @return array
     */
    public function JsonSerialize()
    {
        return [
            'error' => [
                'code' => $this->code,
                'message' => $this->message,
            ],
        ];
    }
}

<?php
namespace pmill\Doctrine\Rest\Exception;

class ValidationException extends RestException
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param array $errors
     * @param int $code
     */
    public function __construct(array $errors, $code = 422)
    {
        $this->errors = $errors;
        parent::__construct(json_encode($errors), $code, null);
    }

    /**
     * @return array
     */
    public function JsonSerialize()
    {
        return [
            'error' => [
                'code' => $this->code,
                'message' => $this->errors,
            ],
        ];
    }
}

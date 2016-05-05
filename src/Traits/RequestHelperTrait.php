<?php
namespace pmill\Doctrine\Rest\Traits;

use Symfony\Component\HttpFoundation\Request;

trait RequestHelperTrait
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @return array
     */
    protected function getRequestPayload()
    {
        return $this->request->request->all();
    }
}

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
        if ($this->request->headers->get('Content-Type') === 'application/json') {
            return json_decode($this->request->getContent(), true);
        }

        return $this->request->request->all();
    }
}

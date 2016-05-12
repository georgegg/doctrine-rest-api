<?php
namespace pmill\Doctrine\Rest\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class MethodAuthentication extends Annotation
{
    /**
     * @var array
     */
    public $value;
}

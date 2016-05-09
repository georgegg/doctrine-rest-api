<?php
namespace pmill\Doctrine\Rest\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class PreUpdate extends Annotation
{
    /**
     * @var string
     */
    public $value;
}

<?php
namespace pmill\Doctrine\Rest\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class Url extends Annotation
{
    /**
     * @var string
     */
    public $entity;

    /**
     * @var string
     */
    public $collection;
}

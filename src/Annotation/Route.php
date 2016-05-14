<?php
namespace pmill\Doctrine\Rest\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class Route extends Annotation
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var string
     */
    public $entity;

    /**
     * @var string
     */
    public $collection;
}

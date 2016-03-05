<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Definition;

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @see    Symfony\Component\DependencyInjection\Reference
 */
final class Reference
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $invalidBehavior;

    /**
     * @param string $id              The service identifier
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @see Container
     */
    public function __construct($id, $invalidBehavior)
    {
        $this->id = $id;
        $this->invalidBehavior = $invalidBehavior;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string The service identifier
     */
    public function __toString()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function throwExceptionOnInvalidBehaviour()
    {
        return BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE === $this->invalidBehavior;
    }

    /**
     * @return bool
     */
    public function returnNullOnInvalidBehaviour()
    {
        return BuilderInterface::NULL_ON_INVALID_REFERENCE === $this->invalidBehavior;
    }

    /**
     * @return bool
     */
    public function ignoreOnInvalidBehaviour()
    {
        return BuilderInterface::IGNORE_ON_INVALID_REFERENCE === $this->invalidBehavior;
    }
}

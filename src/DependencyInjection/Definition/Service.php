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

/**
 * This definition is a simple object representing a service. It encapsulate the data required to instantiate the
 * service and register it to the Application container.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class Service
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string[]|Reference[]
     */
    private $arguments;

    /**
     * @var string[]
     */
    private $autowiringTypes;

    /**
     * @var array
     */
    private $tags;

    /**
     * @param string               $name            Name of the service
     * @param string               $class           FQCN of the service
     * @param string[]|Reference[] $arguments       List of arguments passed for the service instantiation
     * @param array|null           $autowiringTypes List of autowired classes
     * @param array|null           $tags
     */
    public function __construct($name, $class, array $arguments = [], array $autowiringTypes = [], array $tags = [])
    {
        $this->name = $name;
        $this->class = $class;
        $this->arguments = $arguments;
        $this->autowiringTypes = $autowiringTypes;
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string[]|Reference[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return string[]
     */
    public function getAutowiringTypes()
    {
        return $this->autowiringTypes;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}

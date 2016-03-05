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
     * @var Argument[]|null
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
     * @param string $name            Name of the service
     * @param string $class           FQCN of the service
     * @param array  $arguments       |null       List of arguments passed for the service instantiation
     * @param array  $autowiringTypes List of autowired classes
     * @param array  $tags
     */
    public function __construct($name, $class, array $arguments = null, array $autowiringTypes, array $tags)
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
     * @return Argument[]|null
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

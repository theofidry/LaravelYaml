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
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class Service implements ServiceInterface
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getAutowiringTypes()
    {
        return $this->autowiringTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ServiceInterface    $service
     * @param DecorationInterface $decoration
     *
     * @return $this
     */
    public static function createFromDecoration(ServiceInterface $service, DecorationInterface $decoration)
    {
        return new self(
            $decoration->getDecoration()[1],
            $service->getClass(),
            $service->getArguments(),
            $service->getAutowiringTypes(),
            $service->getTags()
        );
    }
}

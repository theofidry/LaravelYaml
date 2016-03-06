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
final class FactoryService implements FactoryInterface
{
    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @var array<Reference|string, string>
     */
    private $factory;

    /**
     * @param ServiceInterface $service
     * @param string|Reference $factory
     * @param string           $factoryMethod
     */
    public function __construct(ServiceInterface $service, $factory, $factoryMethod)
    {
        $this->service = $service;
        $this->factory = [$factory, $factoryMethod];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->service->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->service->getClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->service->getArguments();
    }

    /**
     * {@inheritdoc}
     */
    public function getAutowiringTypes()
    {
        return $this->service->getAutowiringTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->service->getTags();
    }

    /**
     * {@inheritdoc}
     */
    public function getFactory()
    {
        return $this->factory;
    }
}

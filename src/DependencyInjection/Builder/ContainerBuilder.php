<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Builder;

use Fidry\LaravelYaml\DependencyInjection\Definition;
use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ContainerBuilder implements BuilderInterface
{
    /**
     * @var Alias[]
     */
    private $aliases = [];

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var ServiceInterface[]
     */
    private $services = [];

    /**
     * @var BuilderInterface|null
     */
    private $parametersBuilder;

    /**
     * @var BuilderInterface|null
     */
    private $aliasesBuilder;

    /**
     * @var BuilderInterface|null
     */
    private $servicesBuilder;

    public function __construct(
        BuilderInterface $parametersBuilder = null,
        BuilderInterface $aliasesBuilder = null,
        BuilderInterface $servicesBuilder = null
    ) {
        $this->parametersBuilder = $parametersBuilder;
        $this->aliasesBuilder = $aliasesBuilder;
        $this->servicesBuilder = $servicesBuilder;
    }

    /**
     * @param string                            $key
     * @param array|Reference|string|Expression $value
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param Alias $alias
     */
    public function addAlias(Alias $alias)
    {
        $this->aliases[$alias->getAlias()] = $alias;
    }

    /**
     * @param ServiceInterface $service
     */
    public function addService(ServiceInterface $service)
    {
        $this->services[$service->getName()] = $service;
    }

    public function build(Application $application)
    {
        $parametersBuilder = (null === $this->parametersBuilder)
            ? new ParametersBuilder($this->parameters)
            : $this->parametersBuilder
        ;
        $parameters = $parametersBuilder->build($application);

        $servicesBuilder = (null === $this->servicesBuilder)
            ? new ServicesBuilder($this->services, $parameters)
            : $this->servicesBuilder
        ;
        $servicesBuilder->build($application);

        $aliasesBuilder = (null === $this->aliasesBuilder)
            ? new AliasesBuilder($this->aliases)
            : $this->aliasesBuilder
        ;
        $aliasesBuilder->build($application);
    }
}

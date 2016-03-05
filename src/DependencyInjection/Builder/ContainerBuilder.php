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
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
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
     * @var Service[]
     */
    private $services = [];

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
     * @param Service $service
     */
    public function addService(Service $service)
    {
        $this->services[$service->getName()] = $service;
    }

    public function build(Application $application)
    {
        $parametersBuilder = new ParametersBuilder($this->parameters);
        $parameters = $parametersBuilder->build($application);

        $servicesBuilder = new ServicesBuilder($this->services, $parameters);
        $servicesBuilder->build($application);

        $aliasesBuilder = new AliasesBuilder($this->aliases);
        $aliasesBuilder->build($application);
    }
}

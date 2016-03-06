<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Builder\Instantiator;

use Fidry\LaravelYaml\DependencyInjection\Definition\FactoryInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ParameterResolverInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ReferenceResolverInterface;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ServiceInstantiator
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var ReferenceResolverInterface
     */
    private $referenceResolver;

    public function __construct(
        ParameterResolverInterface $parameterResolver,
        ReferenceResolverInterface $referenceResolver,
        Application $application
    ) {
        $this->parameterResolver = $parameterResolver;
        $this->referenceResolver = $referenceResolver;
        $this->application = $application;
    }

    /**
     * @param ServiceInterface $service
     *
     * @return object
     */
    public function create(ServiceInterface $service)
    {
        if ($service instanceof FactoryInterface) {
            return $this->factoryInstantiator($service);
        }

        return $this->constructorInstantiator($service);
    }

    /**
     * @param ServiceInterface $service
     *
     * @return object
     */
    private function constructorInstantiator(ServiceInterface $service)
    {
        $constructor = $service->getClass();
        $resolvedArguments = $this->resolveArguments($service);

        return new $constructor(...$resolvedArguments);
    }

    /**
     * @param FactoryInterface $service
     *
     * @return object
     */
    private function factoryInstantiator(FactoryInterface $service)
    {
        $factory = [
            $this->resolveArgument($service->getFactory()[0]),
            $service->getFactory()[1],
        ];
        $resolvedArguments = $this->resolveArguments($service);

        return call_user_func_array($factory, $resolvedArguments);
    }

    /**
     * @param ServiceInterface $service
     *
     * @return array
     */
    private function resolveArguments(ServiceInterface $service)
    {
        $resolvedArguments = [];
        foreach ($service->getArguments() as $argument) {
            $resolvedArguments[] = $this->resolveArgument($argument);
        }

        return $resolvedArguments;
    }

    /**
     * @param Reference|string $argument
     *
     * @return mixed
     */
    private function resolveArgument($argument)
    {
        if ($argument instanceof Reference) {
            return $this->referenceResolver->resolve($argument, $this->application);
        }

        return $this->parameterResolver->resolve($argument);
    }
}

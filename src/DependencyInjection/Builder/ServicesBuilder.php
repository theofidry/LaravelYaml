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

use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
use Fidry\LaravelYaml\DependencyInjection\Resolver\BaseReferenceResolver;
use Fidry\LaravelYaml\DependencyInjection\Resolver\BuiltParameterResolver;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ParameterResolverInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ReferenceResolverInterface;
use Fidry\LaravelYaml\Exception\DependencyInjection\Exception;
use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\Exception as ResolverException;
use Fidry\LaravelYaml\Exception\ServiceNotFoundException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ServicesBuilder implements BuilderInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @var ReferenceResolverInterface
     */
    private $referenceResolver;

    /**
     * @var Service[]
     */
    private $services;

    /**
     * @var ParameterResolverInterface|null
     */
    private $parameterResolver;

    /**
     * @param Service[]                       $services
     * @param array                           $parameters
     * @param ParameterResolverInterface|null $parameterResolver
     * @param ReferenceResolverInterface|null $referenceResolver
     */
    public function __construct(
        array $services,
        array $parameters,
        ParameterResolverInterface $parameterResolver = null,
        ReferenceResolverInterface $referenceResolver = null
    ) {
        $this->services = $services;
        $this->parameters = $parameters;
        $this->parameterResolver = $parameterResolver;
        $this->referenceResolver = (null === $referenceResolver) ? new BaseReferenceResolver() : $referenceResolver;
    }

    public function build(Application $application)
    {
        try {
            $parameterResolver = $this->getParameterResolver($application);
            foreach ($this->services as $service) {
                $this->buildService($service, $parameterResolver, $application);
            }

            return $this->parameters;
        } catch (BindingResolutionException $exception) {
            throw new Exception(sprintf('Could not load "%s" class', ConfigRepository::class), 0, $exception);
        } catch (ResolverException $exception) {
            throw new Exception('Could not resolve the parameters', 0, $exception);
        } catch (\Exception $exception) {
            throw new Exception('Could not build the parameters', 0, $exception);
        }
    }

    /**
     * @param Application $application
     *
     * @return ParameterResolverInterface
     */
    private function getParameterResolver(Application $application)
    {
        if (null === $this->parameterResolver) {
            $config = $application->make(ConfigRepository::class);

            return new BuiltParameterResolver($this->parameters, $config);
        }

        return $this->parameterResolver;
    }

    private function buildService(
        Service $service,
        ParameterResolverInterface $parameterResolver,
        Application $application
    ) {
        $instance = $this->getInstance($service, $parameterResolver, $application);

        $application->instance($service->getName(), $instance);
        $application->bind($service->getClass(), $service->getName());
        $this->bindAutowiringTypes($service, $application);
        $this->tagService($service, $application);
    }

    /**
     * @param Service                    $service
     * @param ParameterResolverInterface $parameterResolver
     * @param Application                $application
     *
     * @return object
     */
    private function getInstance(
        Service $service,
        ParameterResolverInterface $parameterResolver,
        Application $application
    ) {
        $constructor = $service->getClass();
        $resolvedArguments = $this->resolveArguments($service, $parameterResolver, $application);

        return new $constructor(...$resolvedArguments);
    }

    /**
     * @param Service                    $service
     * @param ParameterResolverInterface $parameterResolver
     * @param Application                $application
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    private function resolveArguments(
        Service $service,
        ParameterResolverInterface $parameterResolver,
        Application $application
    ) {
        $resolvedArguments = [];
        foreach ($service->getArguments() as $argument) {
            if ($argument instanceof Reference) {
                $resolvedArguments[] = $this->referenceResolver->resolve($argument, $application);

                continue;
            }

            $resolvedArguments[] = $parameterResolver->resolve($argument);
        }

        return $resolvedArguments;
    }

    private function bindAutowiringTypes(Service $service, Application $application)
    {
        foreach ($service->getAutowiringTypes() as $binding) {
            $application->bind($binding, $service->getName());
        }
    }

    private function tagService(Service $service, Application $application)
    {
        if (count($service->getTags()) === 0) {
            return;
        }

        $application->tag($service->getName(), array_keys($service->getTags()));
    }
}

<?php

namespace Fidry\LaravelYaml\DependencyInjection\Builder;

use Fidry\LaravelYaml\Configuration\Resolver\BuildedParameterResolver;
use Fidry\LaravelYaml\Configuration\Resolver\ReferenceResolver;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
use Fidry\LaravelYaml\Exception\ServiceNotFoundException;
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
     * @var ReferenceResolver
     */
    private $referenceResolver;

    /**
     * @var Service[]
     */
    private $services;

    /**
     * @param Service[]              $services
     * @param array                  $parameters
     * @param ReferenceResolver|null $referenceResolver
     */
    public function __construct(array $services, array $parameters, ReferenceResolver $referenceResolver = null)
    {
        $this->services = $services;
        $this->parameters = $parameters;
        $this->referenceResolver = (null === $referenceResolver) ? new ReferenceResolver() : $referenceResolver;
    }

    public function build(Application $application)
    {
        $parameterResolver = new BuildedParameterResolver($this->parameters, $application['config']);

        foreach ($this->services as $service) {
            $this->buildService($service, $parameterResolver, $application);
        }
    }

    private function buildService(
        Service $service,
        BuildedParameterResolver $parameterResolver,
        Application $application
    ) {
        $application->singleton(
            $service->getName(),
            function (Application $app) use ($service, $parameterResolver) {
                $constructor = $service->getClass();
                $resolvedArguments = $this->resolveArguments($service, $parameterResolver, $app);

                return new $constructor(...$resolvedArguments);
            }
        );
        $application->bind($service->getClass(), $service->getName());
        $this->bindAutowiringTypes($service, $application);
        $this->tagService($service, $application);
    }

    /**
     * @param Service                  $service
     * @param BuildedParameterResolver $parameterResolver
     * @param Application              $application
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    private function resolveArguments(
        Service $service,
        BuildedParameterResolver $parameterResolver,
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
        $application->tag($service->getName(), array_keys($service->getTags()));
    }
}

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

use Fidry\LaravelYaml\DependencyInjection\Builder\Instantiator\ServiceInstantiator;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\BaseReferenceResolver;
use Fidry\LaravelYaml\DependencyInjection\Resolver\BuiltParameterResolver;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ParameterResolverInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ReferenceResolverInterface;
use Fidry\LaravelYaml\Exception\DependencyInjection\Exception;
use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\Exception as ResolverException;
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
     * @var ServiceInterface[]
     */
    private $services;

    /**
     * @var ParameterResolverInterface|null
     */
    private $parameterResolver;

    /**
     * @param ServiceInterface[]              $services
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
            $instantiator = new ServiceInstantiator($parameterResolver, $this->referenceResolver, $application);
            foreach ($this->services as $service) {
                $this->buildService($service, $instantiator, $application);
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
        if (null !== $this->parameterResolver) {
            return $this->parameterResolver;
        }
        $config = $application->make(ConfigRepository::class);

        return new BuiltParameterResolver($this->parameters, $config);
    }

    private function buildService(
        ServiceInterface $service,
        ServiceInstantiator $instantiator,
        Application $application
    ) {
        $application->singleton(
            $service->getName(),
            function () use ($instantiator, $service) {
                return $instantiator->create($service);
            }
        );
        $application->bind($service->getClass(), $service->getName());
        $this->bindAutowiringTypes($service, $application);
        $this->tagService($service, $application);
    }

    private function bindAutowiringTypes(ServiceInterface $service, Application $application)
    {
        foreach ($service->getAutowiringTypes() as $binding) {
            $application->bind($binding, $service->getName());
        }
    }

    private function tagService(ServiceInterface $service, Application $application)
    {
        if (count($service->getTags()) !== 0) {
            $application->tag($service->getName(), array_keys($service->getTags()));
        }
    }
}

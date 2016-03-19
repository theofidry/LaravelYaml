<?php

/**
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
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ServiceBuilder
{
    /**
     * @var ServiceInstantiator
     */
    private $instantiator;

    /**
     * @param ServiceInstantiator        $instantiator
     */
    public function __construct(ServiceInstantiator $instantiator)
    {
        $this->instantiator = $instantiator;
    }

    /**
     * @param ServiceInterface $service
     * @param Application      $application
     */
    public function build(ServiceInterface $service, Application $application)
    {
        $instantiator = $this->instantiator;
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

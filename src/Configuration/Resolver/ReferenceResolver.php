<?php

namespace Fidry\LaravelYaml\Configuration\Resolver;

use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\Exception\ServiceNotFoundException;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ReferenceResolver
{
    /**
     * @param Reference   $reference
     * @param Application $application
     *
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function resolve(Reference $reference, Application $application)
    {
        try {
            return $application[$reference->getId()];
        } catch (\Exception $exception) {
            switch (true) {
                case $reference->throwExceptionOnInvalidBehaviour():
                    throw new ServiceNotFoundException(sprintf('Could not find service "%s"', $reference->getId()));

                case $reference->returnNullOnInvalidBehaviour():
                case $reference->ignoreOnInvalidBehaviour():
                default:
                    return null;
            }
        }
    }
}

<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Resolver;

use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\Exception\ServiceNotFoundException;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class BaseReferenceResolver implements ReferenceResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(Reference $reference, Application $application)
    {
        try {
            return $application->make($reference->getId());
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

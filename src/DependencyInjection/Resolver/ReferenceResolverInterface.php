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
use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\Exception;
use Fidry\LaravelYaml\Exception\ServiceNotFoundException;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ReferenceResolverInterface
{
    /**
     * @param Reference   $reference
     * @param Application $application
     *
     * @return mixed
     *
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function resolve(Reference $reference, Application $application);
}

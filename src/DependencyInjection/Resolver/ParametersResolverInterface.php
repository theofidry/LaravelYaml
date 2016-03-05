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

use Fidry\LaravelYaml\Exception\Exception;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ParametersResolverInterface
{
    /**
     * Resolves all the parameters.
     *
     * @example
     *  ::resolve([
     *      'foo' => 'bar',
     *      'dummy' => '%foo%',
     *  ]
     *  => [
     *      'foo' => 'bar',
     *      'dummy' => 'bar',
     *  ]
     *
     * @param array $parameters
     *
     * @return array
     *
     * @throws Exception
     */
    public function resolve(array $parameters);
}

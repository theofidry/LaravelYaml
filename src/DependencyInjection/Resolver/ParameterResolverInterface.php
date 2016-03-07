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

use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\Exception;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ParameterResolverInterface
{
    /**
     * @param string $value Parameter value
     *
     * @return mixed
     *
     * @throws Exception
     *
     * @example
     *  ::resolve('%foo%') => 'bar'
     *  ::resolve("@dummy") => 'dummy' service instance
     */
    public function resolve($value);
}

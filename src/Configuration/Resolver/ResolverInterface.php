<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Configuration\Resolver;

use Fidry\LaravelYaml\Exception\Exception;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ResolverInterface
{
    /**
     * Resolves services.
     *
     * @param mixed $value
     * @param array $resolving
     *
     * @return mixed
     * @throws Exception
     */
    public function resolve($value);
}

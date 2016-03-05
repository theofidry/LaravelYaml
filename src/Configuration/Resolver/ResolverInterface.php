<?php

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

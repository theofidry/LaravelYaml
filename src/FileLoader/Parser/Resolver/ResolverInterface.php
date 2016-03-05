<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\FileLoader\Parser\Resolver;

use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * While parsing a file content, some values may refer to other services or parameters for example. Class implementing
 * this interface will resolve those values into understandable values for the {@see ContainerBuilder}.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ResolverInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed|Expression|Reference
     */
    public function resolve($value);
}

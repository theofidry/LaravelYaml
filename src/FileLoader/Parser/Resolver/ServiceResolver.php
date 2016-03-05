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

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ServiceResolver implements ResolverInterface
{
    /**
     * Resolves the given value.
     *
     * @example
     *  ::resolve("@=something") => return an Expression
     *  ::resolve("@something") => will resolve the service reference to return a Reference object
     *  ::resolve($arrayValue) => will recursively resolve the values
     *  Other values are left unchanged
     *
     * @param mixed $value
     *
     * @return mixed|Expression|Reference
     */
    public function resolve($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'resolve'], $value);
        }

        if (is_string($value) && 0 === strpos($value, '@=')) {
            return new Expression(substr($value, 2));
        }

        if (is_string($value) && 0 === strpos($value, '@')) {
            return $this->resolveServiceReferenceValue($value);
        }

        return $value;
    }

    /**
     * @param string $value Service reference
     *
     * @example
     *  "@@dummy"
     *  "@?dummy"
     *  "@<something>"
     *
     * @return Reference
     */
    private function resolveServiceReferenceValue($value)
    {
        $value = substr($value, 1);
        $invalidBehavior = BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE;

        if (0 === strpos($value, '@')) {
            $invalidBehavior = null;
        } elseif (0 === strpos($value, '?')) {
            $value = substr($value, 1);
            $invalidBehavior = BuilderInterface::IGNORE_ON_INVALID_REFERENCE;
        }

        return new Reference($value, $invalidBehavior);
    }
}

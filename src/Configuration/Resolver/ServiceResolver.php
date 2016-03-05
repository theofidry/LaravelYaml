<?php

namespace Fidry\LaravelYaml\Configuration\Resolver;

use Fidry\LaravelYaml\Configuration\ResolverInterface;
use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ServiceResolver
{
    /**
     * @param array|string $value
     *
     * @return array|Reference|string|Expression
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
        // $value = "?dummy"
        // $value = "@dummy" (from "@@dummy")
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

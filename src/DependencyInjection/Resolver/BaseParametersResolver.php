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

use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\ParameterCircularReferenceException;
use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\RuntimeException;
use Fidry\LaravelYaml\Exception\Exception;
use Fidry\LaravelYaml\Exception\ParameterNotFoundException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class BaseParametersResolver implements ParametersResolverInterface
{
    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @var ExpressionLanguage|null
     */
    private $expressionLanguage;

    /**
     * @var array|null
     */
    private $parameters;

    /**
     * @var array
     */
    private $resolved = [];

    public function __construct(ConfigRepository $config)
    {
        $this->config = $config;
        $this->defaultValue = spl_object_hash(new \stdClass());
    }

    /**
     * {@inheritdoc}
     *
     * @param array $parameters
     *
     * @return array
     *
     * @throws ParameterCircularReferenceException
     * @throws ParameterNotFoundException
     * @throws Exception
     */
    public function resolve(array $parameters)
    {
        $this->parameters = $parameters;
        foreach ($this->parameters as $key => $value) {
            $value = $this->resolveValue($value, [$key => true]);
            $this->resolved[$key] = $value;
        }

        return $this->resolved;
    }

    /**
     * @param mixed $value
     * @param array $resolving
     *
     * @return mixed
     * @throws ParameterCircularReferenceException
     * @throws ParameterNotFoundException
     */
    private function resolveValue($value, $resolving = [])
    {
        if (is_bool($value) || is_numeric($value)) {
            return $value;
        }

        if (is_array($value)) {
            return $this->resolveArray($value, $resolving);
        }
        
        if ($value instanceof Expression) {
            return $this->getExpressionLanguage()->evaluate($value, array('container' => $this));
        }

        if (is_string($value)) {
            return $this->resolveString($value, $resolving);
        }

        return $value;
    }

    private function resolveArray(array $arrayValue, array $resolving)
    {
        $resolvedValue = [];
        foreach ($arrayValue as $key => $value) {
            $resolvedValue[$key] = $this->resolveValue($value, $resolving);
        }

        return $resolvedValue;
    }

    /**
     * @param $value
     * @param $resolving
     *
     * @return array|mixed
     * @throws ParameterCircularReferenceException
     * @throws ParameterNotFoundException
     */
    private function resolveString($value, array $resolving)
    {
        if (0 === preg_match('/^%([^%\s]+)%$/', $value, $match)) {
            if (false === array_key_exists($value, $resolving)) {
                return $value;
            }

            $key = $value;
        } else {
            $key = $match[1];
        }

        if (array_key_exists($key, $this->parameters)) {
            return $this->resolveParameter($key, $resolving);
        }

        if ($this->config->has($key)) {
            return $this->config->get($key);
        }

        return $this->resolveEnvironmentValue($key);
    }

    /**
     * @param string $key
     * @param array  $resolving
     *
     * @return array|mixed
     * @throws ParameterCircularReferenceException
     * @throws ParameterNotFoundException
     */
    private function resolveParameter($key, array $resolving)
    {
        if (array_key_exists($key, $this->resolved)) {
            return $this->resolved[$key];
        }

        if (array_key_exists($key, $resolving)) {
            throw new ParameterCircularReferenceException(
                sprintf(
                    'Circular reference detected for the parameter "%s" while resolving [%s]',
                    $key,
                    implode(', ', array_keys($resolving))
                )
            );
        }
        $resolving[$key] = true;
        $this->resolved[$key] = $this->resolveValue($this->parameters[$key], $resolving);

        return $this->resolved[$key];
    }

    /**
     * @param string $key
     *
     * @return string|int|bool|null
     * @throws ParameterNotFoundException
     */
    private function resolveEnvironmentValue($key)
    {
        $environmentKey = strtoupper(str_replace('.', '_', $key));
        $value = env($environmentKey, $this->defaultValue);
        if ($this->defaultValue !== $value) {
            return $value;
        }

        throw new ParameterNotFoundException(sprintf('No parameter "%s" found', $key));
    }

    /**
     * @return ExpressionLanguage
     * @throws RuntimeException
     */
    private function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            if (!class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
                throw new RuntimeException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed.');
            }
            $this->expressionLanguage = new ExpressionLanguage();
        }

        return $this->expressionLanguage;
    }
}

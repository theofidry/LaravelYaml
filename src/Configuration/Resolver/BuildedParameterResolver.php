<?php

namespace Fidry\LaravelYaml\Configuration\Resolver;

use Fidry\LaravelYaml\Exception\Configuration\Resolver\ParameterCircularReferenceException;
use Fidry\LaravelYaml\Exception\ParameterNotFoundException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class BuildedParameterResolver implements ResolverInterface
{
    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $resolved = [];

    public function __construct(array $parameters, ConfigRepository $config)
    {
        $this->parameters = $parameters;
        $this->config = $config;
        $this->defaultValue = spl_object_hash(new \stdClass());
    }

    /**
     * @return array
     */
    public function resolve($key)
    {
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
            return $this->parameters[$key];
        }

        if ($this->config->has($key)) {
            return $this->config->get($key);
        }

        return $this->resolveEnvironmentValue($key);
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
}

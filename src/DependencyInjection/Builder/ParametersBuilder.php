<?php

namespace Fidry\LaravelYaml\DependencyInjection\Builder;

use Fidry\LaravelYaml\Configuration\Resolver\ParameterResolver;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ParametersBuilder implements BuilderInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function build(Application $application)
    {
        $resolver = new ParameterResolver($this->parameters, $application['config']);
        $parameters = $resolver->resolve();
        foreach ($parameters as $key => $value) {
            $application[$key] = $value;
        }

        return $this->parameters;
    }
}

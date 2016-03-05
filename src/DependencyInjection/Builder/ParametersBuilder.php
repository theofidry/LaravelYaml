<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Builder;

use Fidry\LaravelYaml\DependencyInjection\Resolver\BaseParametersResolver;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ParametersResolverInterface;
use Fidry\LaravelYaml\Exception\DependencyInjection\Exception;
use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\Exception as ResolverException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
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
     * @var ParametersResolverInterface|null
     */
    private $resolver;

    /**
     * @param array                            $parameters
     * @param ParametersResolverInterface|null $resolver
     */
    public function __construct(array $parameters, ParametersResolverInterface $resolver = null)
    {
        $this->parameters = $parameters;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function build(Application $application)
    {
        try {
            $configRepository = $application->make(ConfigRepository::class);
            $resolver = (null === $this->resolver)
                ? new BaseParametersResolver($configRepository)
                : $this->resolver
            ;

            $parameters = $resolver->resolve($this->parameters);
            foreach ($parameters as $key => $value) {
                $application->bind($key, $value);
            }

            return $this->parameters;
        } catch (BindingResolutionException $exception) {
            throw new Exception(sprintf('Could not load "%s" class', ConfigRepository::class), 0, $exception);
        } catch (ResolverException $exception) {
            throw new Exception('Could not resolve the parameters', 0, $exception);
        } catch (\Exception $exception) {
            throw new Exception('Could not build the parameters', 0, $exception);
        }
    }
}

<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\FileLoader\Parser\Yaml;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;
use Fidry\LaravelYaml\FileLoader\Parser\ParserInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ServiceResolver;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ParametersParser implements ParserInterface
{
    /**
     * @var ResolverInterface
     */
    private $serviceResolver;

    public function __construct(ResolverInterface $serviceResolver = null)
    {
        $this->serviceResolver = (null === $serviceResolver)? new ServiceResolver(): $serviceResolver;
    }

    /**
     * Parses parameters and register them to the container.
     *
     * @param ContainerBuilder $container
     * @param array            $content  YAML file content
     * @param string           $fileName file name
     *
     * @throws InvalidArgumentException
     */
    public function parse(ContainerBuilder $container, $content, $fileName)
    {
        if (false === isset($content['parameters'])) {
            return;
        }

        if (false === is_array($content['parameters'])) {
            throw new InvalidArgumentException(
                sprintf('The "parameters" key should contain an array in %s. Check your YAML syntax.', $fileName)
            );
        }

        foreach ($content['parameters'] as $key => $value) {
            $container->setParameter(strtolower($key), $this->serviceResolver->resolve($value));
        }
    }
}

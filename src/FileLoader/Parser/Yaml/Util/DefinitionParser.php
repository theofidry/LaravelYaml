<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\FileLoader\Parser\Yaml\Util;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DefinitionParser
{
    /**
     * @var ServiceParser
     */
    private $serviceParser;

    public function __construct(ResolverInterface $serviceResolver)
    {
        $this->serviceParser = new ServiceParser($serviceResolver);
    }

    /**
     * Parses a service definition and register it to the container.
     *
     * @param ContainerBuilder $container
     * @param string           $id
     * @param array|string     $service
     * @param string           $fileName file name
     *
     * @throws InvalidArgumentException
     */
    public function parse(ContainerBuilder $container, $id, $service, $fileName)
    {
        if (is_string($service) && 0 === strpos($service, '@')) {
            $alias = new Alias($id, substr($service, 1));
            $container->addAlias($alias);

            return;
        }

        if (false === is_array($service)) {
            throw new InvalidArgumentException(
                sprintf(
                    'A service definition must be an array or a string starting with "@" but %s found for service "%s" in %s. Check your YAML syntax.',
                    gettype($service),
                    $id,
                    $fileName
                )
            );
        }

        $this->serviceParser->parse($container, $id, $service, $fileName);
    }
}

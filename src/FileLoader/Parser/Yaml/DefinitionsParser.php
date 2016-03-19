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
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ServiceResolver;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\Util\DefinitionParser;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DefinitionsParser
{
    /**
     * @var DefinitionParser
     */
    private $definitionParser;

    public function __construct(ResolverInterface $serviceResolver = null)
    {
        $serviceResolver = (null === $serviceResolver) ? new ServiceResolver() : $serviceResolver;
        $this->definitionParser = new DefinitionParser($serviceResolver);
    }

    /**
     * Parses service definitions and register them to the container.
     *
     * @param ContainerBuilder $container
     * @param array            $content  YAML file content
     * @param string           $fileName file name
     *
     * @throws InvalidArgumentException
     */
    public function parse(ContainerBuilder $container, $content, $fileName)
    {
        if (!isset($content['services'])) {
            return;
        }

        if (!is_array($content['services'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'The "services" key should contain an array in %s. Check your YAML syntax.',
                    $fileName
                )
            );
        }

        foreach ($content['services'] as $id => $service) {
            $this->definitionParser->parse($container, strtolower($id), $service, $fileName);
        }
    }
}

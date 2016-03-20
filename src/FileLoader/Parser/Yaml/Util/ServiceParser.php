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
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ServiceParser
{
    /**
     * @var AliasParser
     */
    private $aliasParser;

    /**
     * @var AutowiringTypesParser
     */
    private $autowiringTypesParser;

    /**
     * @var ClassParser
     */
    private $classParser;

    /**
     * @var DecorationParser
     */
    private $decorationParser;

    /**
     * @var FactoryParser
     */
    private $factoryParser;

    /**
     * @var ResolverInterface
     */
    private $serviceResolver;

    /**
     * @var TagsParser
     */
    private $tagsParser;

    public function __construct(ResolverInterface $serviceResolver)
    {
        $this->serviceResolver = $serviceResolver;

        $this->aliasParser = new AliasParser();
        $this->classParser = new ClassParser();
        $this->tagsParser = new TagsParser();
        $this->autowiringTypesParser = new AutowiringTypesParser();
        $this->factoryParser = new FactoryParser($serviceResolver);
        $this->decorationParser = new DecorationParser();
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
    public function parse(ContainerBuilder $container, $id, array $service, $fileName)
    {
        $alias = $this->aliasParser->parse($id, $service, $fileName);
        if (null !== $alias) {
            $container->addAlias($alias);
        }

        $class = $this->classParser->parse($id, $service, $fileName);
        $arguments = (isset($service['arguments']))
            ? $this->serviceResolver->resolve($service['arguments'])
            : []
        ;
        $tags = $this->tagsParser->parse($id, $service, $fileName);
        $autowiringTypes = $this->autowiringTypesParser->parse($id, $service, $fileName);

        $serviceDefinition = $this->createService(
            $id,
            $class,
            $arguments,
            $autowiringTypes,
            $tags,
            $service,
            $fileName
        );

        $container->registerService($serviceDefinition);
    }
    
    private function createService($id, $class, array $arguments, array $autowiringTypes, array $tags, array $service, $fileName)
    {
        $serviceDefinition = new Service($id, $class, $arguments, $autowiringTypes, $tags);

        if (isset($service['factory'])) {
            return $this->factoryParser->parse($serviceDefinition, $service['factory'], $fileName);
        }
        
        if (isset($service['decorates'])) {
            return $this->decorationParser->parse($serviceDefinition, $service, $fileName);
        }
        
        return $serviceDefinition;
    }
}

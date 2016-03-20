<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\FileLoader\Yaml;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\Exception\FileLoader\Exception;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;
use Fidry\LaravelYaml\FileLoader\FileLoaderInterface;
use Fidry\LaravelYaml\FileLoader\Parser\ParserInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\DefinitionsParser;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\ImportsParser;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\ParametersParser;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * This loader is able to load YAML files. Parsed values are interpreted and added to the {@see ContainerBuilder} to be
 * loaded to the Application later on.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class YamlFileLoader implements FileLoaderInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var ParserInterface
     */
    private $definitionsParser;

    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var ParserInterface
     */
    private $importsParser;

    /**
     * @var ParserInterface
     */
    private $parametersParser;

    /**
     * @var YamlSingleFileLoader
     */
    private $singleFileLoader;

    public function __construct(
        ContainerBuilder $container,
        FileLocatorInterface $fileLocator,
        ParserInterface $definitionsParser = null,
        ParserInterface $parametersParser = null,
        ParserInterface $importsParser = null,
        YamlParser $yamlParser = null,
        YamlValidator $yamlValidator = null
    ) {
        $this->container = $container;
        $this->fileLocator = $fileLocator;

        $this->definitionsParser = (null === $definitionsParser) ? new DefinitionsParser() : $definitionsParser;
        $this->parametersParser = (null === $parametersParser) ? new ParametersParser() : $parametersParser;
        $this->importsParser = (null === $importsParser) ? new ImportsParser() : $importsParser;

        $yamlParser = (null === $yamlParser) ? new YamlParser() : $yamlParser;
        $yamlValidator = (null === $yamlValidator) ? new YamlValidator() : $yamlValidator;
        $this->singleFileLoader = new YamlSingleFileLoader($yamlParser, $yamlValidator);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $resource file name
     *
     * @example
     *  ::load('services.yml')
     *
     * @throws InvalidArgumentException
     * @throws Exception
     * @return $this
     */
    public function load($resource)
    {
        /* @var string|null $path */
        $path = $this->fileLocator->locate($resource, null, true);
        $content = $this->singleFileLoader->loadFile($path);

        $imports = $this->importsParser->parse($this->container, $content, $resource);
        foreach ($imports as $importedResource) {
            $this->load($importedResource);
        }
        $this->parametersParser->parse($this->container, $content, $resource);
        $this->definitionsParser->parse($this->container, $content, $resource);

        return $this;
    }
}

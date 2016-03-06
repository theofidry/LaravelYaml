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
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\ParametersParser;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Exception\ParseException;
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
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var ParserInterface
     */
    private $definitionsParser;

    /**
     * @var ParserInterface
     */
    private $parametersParser;

    /**
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * @var YamlValidator
     */
    private $yamlValidator;

    public function __construct(
        ContainerBuilder $container,
        FileLocatorInterface $fileLocator,
        ParserInterface $definitionsParser = null,
        ParserInterface $parametersParser = null,
        YamlParser $yamlParser = null,
        YamlValidator $yamlValidator = null
    ) {
        $this->container = $container;
        $this->fileLocator = $fileLocator;

        $this->definitionsParser = (null === $definitionsParser) ? new DefinitionsParser() : $definitionsParser;
        $this->parametersParser = (null === $parametersParser) ? new ParametersParser() : $parametersParser;
        $this->yamlParser = (null === $yamlParser) ? new YamlParser() : $yamlParser;
        $this->yamlValidator = (null === $yamlValidator) ? new YamlValidator() : $yamlValidator;
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
        $content = $this->loadFile($path);

        $this->parametersParser->parse($this->container, $content, $resource);
        $this->definitionsParser->parse($this->container, $content, $resource);

        return $this;
    }

    /**
     * @param string $filePath
     *
     * @return array The file content
     * @throws InvalidArgumentException
     */
    private function loadFile($filePath)
    {
        if (false === stream_is_local($filePath)) {
            throw new InvalidArgumentException(sprintf('This is not a local file "%s".', $filePath));
        }

        if (false === file_exists($filePath)) {
            throw new InvalidArgumentException(sprintf('The service file "%s" is not valid.', $filePath));
        }

        try {
            $configuration = $this->yamlParser->parse(file_get_contents($filePath));
        } catch (ParseException $exception) {
            throw new InvalidArgumentException(
                sprintf('The file "%s" does not contain valid YAML.', $filePath), 0, $exception
            );
        }

        return $this->yamlValidator->validate($configuration, $filePath);
    }
}

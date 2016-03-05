<?php

/**
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fidry\LaravelYaml\FileLoader;

use Fidry\LaravelYaml\Configuration\Validator\YamlValidator;
use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\Exception\Loader\InvalidArgumentException;
use Fidry\LaravelYaml\FileLoaderInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\DefinitionsParser;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\ParametersParser;
use Symfony\Component\Config\FileLocator;
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
     * @var FileLocator
     */
    private $fileLocator;

    /**
     * @var DefinitionsParser
     */
    private $definitionsParser;

    /**
     * @var ParametersParser
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
        FileLocator $fileLocator,
        DefinitionsParser $definitionsParser = null,
        ParametersParser $parametersParser = null,
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
     */
    public function load($resource)
    {
        $path = $this->fileLocator->locate($resource);
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

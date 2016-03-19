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

use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class YamlSingleFileLoader
{
    /**
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * @var YamlValidator
     */
    private $yamlValidator;

    public function __construct(YamlParser $yamlParser, YamlValidator $yamlValidator)
    {
        $this->yamlParser = $yamlParser;
        $this->yamlValidator = $yamlValidator;
    }

    /**
     * @param string $filePath
     *
     * @return array The file content
     * @throws InvalidArgumentException
     */
    public function loadFile($filePath)
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

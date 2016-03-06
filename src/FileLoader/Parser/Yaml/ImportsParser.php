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
class ImportsParser implements ParserInterface
{
    /**
     * Parses import statements and return resources.
     *
     * @param ContainerBuilder $containerBuilder
     * @param array            $content  YAML file content
     * @param string           $fileName file name
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function parse(ContainerBuilder $containerBuilder, $content, $fileName)
    {
        if (false === isset($content['imports'])) {
            return [];
        }

        if (false === is_array($content['imports'])) {
            throw new InvalidArgumentException(
                sprintf('The "imports" key should contain an array in %s. Check your YAML syntax.', $fileName)
            );
        }

        $imports = [];
        foreach ($content['imports'] as $importStatement) {
            if (false === isset($importStatement['resource']) || false === is_string($importStatement['resource'])) {
                throw new InvalidArgumentException(
                    sprintf('The "imports" objects should contain a "resource" key in %s. Check your YAML syntax.', $fileName)
                );
            }

            $imports[$importStatement['resource']] = true;
        }

        return array_keys($imports);
    }
}

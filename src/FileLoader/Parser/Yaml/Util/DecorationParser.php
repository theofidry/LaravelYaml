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

use Fidry\LaravelYaml\DependencyInjection\Definition\Decoration;
use Fidry\LaravelYaml\DependencyInjection\Definition\DecorationInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DecorationParser
{
    /**
     * Parses a factory service definition and return the decoration object.
     *
     * @param ServiceInterface $service
     * @param mixed            $decoration
     * @param string           $fileName file name
     *
     * @return DecorationInterface
     * @throws InvalidArgumentException
     */
    public function parse(ServiceInterface $service, $decoration, $fileName)
    {
        if (false === is_string($decoration['decorates'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "decorates" for service "%s" must be the id of another service, but found "%s" instead in %s. Check your YAML syntax.',
                    $service->getName(),
                    gettype($decoration['decorates']),
                    $fileName
                )
            );
        }
        $decorates = $decoration['decorates'];

        $decorationInnerName = (isset($decoration['decoration_inner_name']))
            ? $decoration['decoration_inner_name']
            : null
        ;
        if (null !== $decorationInnerName && false === is_string($decorationInnerName)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "decoration_inner_name" for service "%s" must be a string if is set, but found "%s" instead in %s. Check your YAML syntax.',
                    $service->getName(),
                    gettype($decorationInnerName),
                    $fileName
                )
            );
        }

        return new Decoration($service, $decorates, $decorationInnerName);
    }
}

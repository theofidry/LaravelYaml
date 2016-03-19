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

use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ClassParser
{
    /**
     * @param string $id
     * @param array  $service
     * @param string $fileName
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function parse($id, $service, $fileName)
    {
        if (false === isset($service['class'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "class" missing for service "%s" in %s. Check your YAML syntax.',
                    $id,
                    $fileName
                )
            );
        }
        if (false === is_string($service['class'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "class" must be a FQCN for service "%s", but found "%s" instead in %s. Check your YAML syntax.',
                    $id,
                    gettype($service['class']),
                    $fileName
                )
            );
        }

        return $service['class'];
    }
}

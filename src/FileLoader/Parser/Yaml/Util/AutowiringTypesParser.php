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
final class AutowiringTypesParser
{
    /**
     * @param string $id
     * @param array  $service
     * @param string $fileName
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function parse($id, $service, $fileName)
    {
        if (false === isset($service['autowiringTypes'])) {
            return [];
        }

        if (false === is_array($service['autowiringTypes'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "autowiringTypes" must be an array for service "%s" in %s. Check your YAML syntax.',
                    $id,
                    $fileName
                )
            );
        }

        $autowiringTypes = [];
        foreach ($service['autowiringTypes'] as $autowiringType) {
            if (false === is_string($autowiringType)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'A "autowiringType" entry must be a FQCN for service "%s" in %s. Check your YAML syntax.',
                        $id,
                        $fileName
                    )
                );
            }

            $autowiringTypes[$autowiringType] = true;
        }

        return array_keys($autowiringTypes);
    }
}

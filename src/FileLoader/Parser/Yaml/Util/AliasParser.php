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

use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class AliasParser
{
    /**
     * Parses a service definition alias and register it to the container.
     *
     * @param string           $id
     * @param array|string     $service
     * @param string           $fileName file name
     *
     * @throws InvalidArgumentException
     * 
     * @return Alias|null
     */
    public function parse($id, array $service, $fileName)
    {
        if (false === isset($service['alias'])) {
            return null;
        }
        
        $aliasName = $service['alias'];
        if (false === is_string($aliasName)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "alias" must be a plain name for service "%s", but found "%s" instead in %s. Check your YAML syntax.',
                    $id,
                    gettype($aliasName),
                    $fileName
                )
            );
        }

        return new Alias($aliasName, $id);
    }
}

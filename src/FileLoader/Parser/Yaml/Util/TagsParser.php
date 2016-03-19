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
final class TagsParser
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
        if (false === isset($service['tags'])) {
            return [];
        }

        if (false === is_array($service['tags'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "tags" must be an array for service "%s" in %s. Check your YAML syntax.',
                    $id,
                    $fileName
                )
            );
        }
        $tags = $this->parseTags($id, $service['tags'], $fileName);

        return $tags;
    }

    /**
     * @param string $id
     * @param array  $tags
     * @param string $fileName
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function parseTags($id, array $tags, $fileName)
    {
        $parsedTags= [];
        foreach ($tags as $tag) {
            $this->checkTag($id, $tag, $fileName);

            $name = strtolower($tag['name']);
            unset($tag['name']);

            $parsedTags[$name] = $tag;
        }

        return $parsedTags;
    }

    /**
     * @param string $id
     * @param  mixed $tag
     * @param string $fileName
     *
     * @throws InvalidArgumentException
     */
    private function checkTag($id, $tag, $fileName)
    {
        if (false === is_array($tag)) {
            throw new InvalidArgumentException(
                sprintf(
                    'A "tags" entry must be an array for service "%s" in %s. Check your YAML syntax.',
                    $id,
                    $fileName
                )
            );
        }

        if (false === isset($tag['name'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'A "tags" entry is missing a "name" key for service "%s" in %s.',
                    $id,
                    $fileName
                )
            );
        }
    }
}

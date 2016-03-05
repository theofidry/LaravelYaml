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

use Fidry\LaravelYaml\Exception\Configuration\InvalidArgumentException;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class YamlValidator
{
    /**
     * Checks that the content returned by the YAML parser has an understandable definition.
     *
     * @param $content
     * @param $file
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function validate($content, $file)
    {
        if (null === $content) {
            return $content;
        }

        if (false === is_array($content)) {
            throw new InvalidArgumentException(
                sprintf('The service file "%s" is not valid. It should contain an array. Check your YAML syntax.', $file)
            );
        }

        foreach ($content as $namespace => $data) {
            if (in_array($namespace, ['parameters', 'services'])) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Invalid namespace name "%s" in "%s".', $namespace, $file)
            );
        }

        return $content;
    }
}

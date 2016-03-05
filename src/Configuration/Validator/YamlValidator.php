<?php

namespace Fidry\LaravelYaml\Configuration\Validator;

use Fidry\LaravelYaml\Exception\Configuration\InvalidArgumentException;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class YamlValidator
{
    /**
     * {@inheritdoc}
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

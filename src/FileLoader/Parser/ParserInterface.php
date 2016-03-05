<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\FileLoader\Parser;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\Exception\Loader\Exception;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ParserInterface
{
    /**
     * Parses the result of the YAML parser to register the content to the container.
     *
     * @param ContainerBuilder $container
     * @param array            $content  YAML file content
     * @param string           $fileName file name
     *
     * @throws Exception
     */
    public function parse(ContainerBuilder $container, $content, $fileName);
}

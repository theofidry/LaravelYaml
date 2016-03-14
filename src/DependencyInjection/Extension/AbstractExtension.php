<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Extension;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\FileLoader\FileLoaderInterface;
use Fidry\LaravelYaml\FileLoader\Yaml\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     *
     * @return YamlFileLoader
     */
    public function load(ContainerBuilder $container)
    {
        $resourcePath = (function_exists('resource_path'))
            ? resource_path('providers')
            : app('path').DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'providers'
        ;

        $rootDir = new FileLocator($resourcePath);
        $loader = new YamlFileLoader($container, $rootDir);

        return $loader;
    }

    /**
     * @param FileLoaderInterface $loader
     * @param string              $resource
     *
     * @return $this
     */
    protected function loadResourceIfExist(FileLoaderInterface $loader, $resource)
    {
        try {
            $loader->load($resource);
        } catch (\InvalidArgumentException $exception) {
            // Ignore error as is an optional file
        }

        return $this;
    }
}

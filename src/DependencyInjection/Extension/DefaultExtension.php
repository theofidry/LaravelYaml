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
use Illuminate\Support\Facades\App;
use Symfony\Component\Config\FileLocator;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DefaultExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container)
    {
        $rootDir = new FileLocator(resource_path('providers'));
        $loader = new YamlFileLoader($container, $rootDir);

        $this
            ->loadResourceIfExist($loader, 'parameters.yml')
            ->loadResourceIfExist($loader, 'services.yml')
            ->loadResourceIfExist($loader, sprintf('parameters_%s.yml', App::environment()))
        ;
    }

    /**
     * @param FileLoaderInterface $loader
     * @param string              $resource
     *
     * @return $this
     */
    private function loadResourceIfExist(FileLoaderInterface $loader, $resource)
    {
        try {
            $loader->load($resource);
        } catch (\InvalidArgumentException $exception) {
            // Ignore error as is an optional file
        }

        return $this;
    }
}

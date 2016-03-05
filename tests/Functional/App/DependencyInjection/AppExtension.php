<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Functional\App\DependencyInjection;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Extension\ExtensionInterface;
use Fidry\LaravelYaml\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

final class AppExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(resource_path('/services')));
        $loader
            ->load('parameters.yml')
            ->load('parameters_test.yml')
            ->load('services.yml')
            ->load('service_2.yml')
        ;
    }
}

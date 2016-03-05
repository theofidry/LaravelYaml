<?php

namespace App\DependencyInjection;

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

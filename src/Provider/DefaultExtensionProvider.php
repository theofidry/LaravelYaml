<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Provider;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Extension\DefaultExtension;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DefaultExtensionProvider extends IlluminateServiceProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $container = new ContainerBuilder();

        $extension = new DefaultExtension();
        $extension->load($container);

        $container->build($this->app);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return [DefaultExtension::class];
    }
}

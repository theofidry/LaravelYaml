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
use Illuminate\Support\Facades\App;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DefaultExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container)
    {
        $loader = parent::load($container);

        $this
            ->loadResourceIfExist($loader, 'parameters.yml')
            ->loadResourceIfExist($loader, 'services.yml')
            ->loadResourceIfExist($loader, sprintf('parameters_%s.yml', App::environment()))
        ;
    }
}

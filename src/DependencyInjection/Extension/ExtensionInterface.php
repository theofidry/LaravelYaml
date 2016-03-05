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

/**
 * An extension is responsible for loading configuration files.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ExtensionInterface
{
    /**
     * Load configuration files to add them to the container builder.
     *
     * @param ContainerBuilder $container
     *
     * @return mixed
     */
    public function load(ContainerBuilder $container);
}

<?php

namespace Fidry\LaravelYaml\DependencyInjection\Extension;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ExtensionInterface
{
    public function load(ContainerBuilder $container);
}

<?php

namespace Fidry\LaravelYaml\Provider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @return string[] FQCN of {@see Fidry\LaravelYaml\DependencyInjection\Extension\ExtensionInterface} to load
     */
    public function getExtensions();
}

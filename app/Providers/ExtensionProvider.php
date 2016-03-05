<?php

namespace App\Providers;

use Fidry\LaravelYaml\Provider\AbstractExtensionProvider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ExtensionProvider extends AbstractExtensionProvider
{
    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return [
            \App\DependencyInjection\AppExtension::class,
        ];
    }
}

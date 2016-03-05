<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Functional\App\Providers;

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
            \Fidry\LaravelYaml\Functional\App\DependencyInjection\AppExtension::class,
        ];
    }
}

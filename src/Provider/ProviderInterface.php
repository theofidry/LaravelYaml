<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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

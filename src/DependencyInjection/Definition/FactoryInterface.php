<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Definition;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface FactoryInterface extends ServiceInterface
{
    /**
     * @return array<string|Reference> The first value (string or Reference) is the class or reference to the factory
     *                                 service and the second argument (string) is the method used to instantiate the
     *                                 service
     */
    public function getFactory();
}

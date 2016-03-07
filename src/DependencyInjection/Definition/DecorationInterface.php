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
interface DecorationInterface extends ServiceInterface
{
    /**
     * @return array<string, string> The first value is the service id of the decorated service. The second argument is
     *                               the new name of the decorated service.
     */
    public function getDecoration();
}

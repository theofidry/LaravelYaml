<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Builder;

use Fidry\LaravelYaml\Exception\DependencyInjection\Exception;
use Illuminate\Contracts\Foundation\Application;

/**
 * Builders are the classes responsible for adding to the application a definition.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface BuilderInterface
{
    const EXCEPTION_ON_INVALID_REFERENCE = 0;
    const NULL_ON_INVALID_REFERENCE = 1;
    const IGNORE_ON_INVALID_REFERENCE = 2;

    /**
     * Builds the builder definitions.
     *
     * @param Application $application
     *
     * @throws Exception
     */
    public function build(Application $application);
}

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

use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface BuilderInterface
{
    const EXCEPTION_ON_INVALID_REFERENCE = 0;
    const NULL_ON_INVALID_REFERENCE = 1;
    const IGNORE_ON_INVALID_REFERENCE = 2;

    /**
     * @param Application $application
     */
    public function build(Application $application);
}

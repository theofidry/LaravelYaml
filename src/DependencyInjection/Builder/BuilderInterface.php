<?php

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

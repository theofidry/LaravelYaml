<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Test;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class DummyFactory
{
    /**
     * @var DummyService
     */
    private $dummy;

    public function __construct(DummyService $dummy)
    {
        $this->dummy = $dummy;
    }

    /**
     * @param array ...$args
     *
     * @return AnotherDummyService
     */
    public function create(...$args)
    {
        $args[] = $this->dummy;

        return new AnotherDummyService(...$args);
    }
}

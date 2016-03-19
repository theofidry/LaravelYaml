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
class UnwirableDummy
{
    /**
     * @var DummyInterface
     */
    private $interfaceArg;
    /**
     * @var array
     */
    private $args;

    public function __construct(DummyInterface $interfaceArg, ...$args)
    {
        $this->interfaceArg = $interfaceArg;
        $this->args = $args;
    }

    /**
     * @return DummyInterface
     */
    public function getInterfaceArg()
    {
        return $this->interfaceArg;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }
}

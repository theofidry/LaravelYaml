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
 * This definition is a simple object encapsulating the required data to register an alias to the Application container.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class Alias
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $aliased;

    /**
     * @param string $alias  Alias name
     * @param string $aliased Name of the service for which the alias is for
     */
    public function __construct($alias, $aliased)
    {
        $this->alias = $alias;
        $this->aliased = $aliased;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function getAliased()
    {
        return $this->aliased;
    }
}

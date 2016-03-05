<?php

namespace Fidry\LaravelYaml\DependencyInjection\Definition;

/**
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

<?php

namespace Fidry\LaravelYaml\DependencyInjection\Builder;

use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class AliasesBuilder implements BuilderInterface
{
    /**
     * @var array|Alias[]
     */
    private $aliases;

    /**
     * @param Alias[] $aliases
     */
    public function __construct(array $aliases)
    {
        $this->aliases = $aliases;
    }

    public function build(Application $application)
    {
        foreach ($this->aliases as $alias) {
            $this->buildAlias($alias, $application);
        }
    }

    private function buildAlias(Alias $alias, Application $application)
    {
        $application->alias($alias->getAliased(), $alias->getAlias());
    }
}

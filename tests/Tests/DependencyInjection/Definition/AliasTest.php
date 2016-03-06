<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\DependencyInjection\Definition;

use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Definition\Alias
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class AliasTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $alias = new Alias('aliasName', 'aliasedServiceName');

        $this->assertEquals('aliasName', $alias->getAlias());
        $this->assertEquals('aliasedServiceName', $alias->getAliased());
    }
}

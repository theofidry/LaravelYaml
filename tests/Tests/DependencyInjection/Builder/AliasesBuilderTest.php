<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\DependencyInjection\Builder;

use Fidry\LaravelYaml\DependencyInjection\Builder\AliasesBuilder;
use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Builder\AliasesBuilder
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class AliasesBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideAliases
     */
    public function testBuildAliases($application, $aliases)
    {
        $builder = new AliasesBuilder($aliases);
        $builder->build($application);

        $this->assertTrue(true);
    }

    public function provideAliases()
    {
        $aliases = [];

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->alias(Argument::cetera())->shouldNotBeCalled();

        yield [$applicationProphecy->reveal(), $aliases];

        $aliases = [
            new Alias('foo', 'bar'),
            new Alias('dummy', 'dummyAliasName'),
        ];

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->alias('bar', 'foo')->shouldBeCalledTimes(1);
        $applicationProphecy->alias('dummyAliasName', 'dummy')->shouldBeCalledTimes(1);

        yield [$applicationProphecy->reveal(), $aliases];
    }
}

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

use Fidry\LaravelYaml\DependencyInjection\Definition\Decoration;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Definition\Decoration
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class DecorationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $serviceProphecy = $this->prophesize(ServiceInterface::class);
        $serviceProphecy->getClass()->shouldBeCalledTimes(2);
        $serviceProphecy->getClass()->willReturn('App\Dummy');
        $serviceProphecy->getArguments()->shouldBeCalledTimes(2);
        $serviceProphecy->getArguments()->willReturn([]);
        $serviceProphecy->getAutowiringTypes()->shouldBeCalledTimes(2);
        $serviceProphecy->getAutowiringTypes()->willReturn([]);
        $serviceProphecy->getTags()->shouldBeCalledTimes(2);
        $serviceProphecy->getTags()->willReturn([]);
        /* @var ServiceInterface $service */
        $service = $serviceProphecy->reveal();

        $factory = new Decoration($service, 'foo', null);

        $this->assertEquals('foo', $factory->getName());
        $this->assertEquals('App\Dummy', $factory->getClass());
        $this->assertEquals([], $factory->getArguments());
        $this->assertEquals([], $factory->getAutowiringTypes());
        $this->assertEquals([], $factory->getTags());
        $this->assertEquals(['foo', 'foo.inner'], $factory->getDecoration());

        $factory = new Decoration($service, 'foo', 'bar');

        $this->assertEquals('foo', $factory->getName());
        $this->assertEquals('App\Dummy', $factory->getClass());
        $this->assertEquals([], $factory->getArguments());
        $this->assertEquals([], $factory->getAutowiringTypes());
        $this->assertEquals([], $factory->getTags());
        $this->assertEquals(['foo', 'bar'], $factory->getDecoration());
    }
}

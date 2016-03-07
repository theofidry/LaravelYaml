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
    public function testConstructWithDefaultValues()
    {
        $arg = new \stdClass();
        $type = new \stdClass();
        $tag = new \stdClass();

        $serviceProphecy = $this->prophesize(ServiceInterface::class);
        $serviceProphecy->getName()->shouldBeCalledTimes(2);
        $serviceProphecy->getName()->willReturn('bar');
        $serviceProphecy->getClass()->shouldBeCalledTimes(1);
        $serviceProphecy->getClass()->willReturn('App\Dummy');
        $serviceProphecy->getArguments()->shouldBeCalledTimes(1);
        $serviceProphecy->getArguments()->willReturn([$arg]);
        $serviceProphecy->getAutowiringTypes()->shouldBeCalledTimes(1);
        $serviceProphecy->getAutowiringTypes()->willReturn([$type]);
        $serviceProphecy->getTags()->shouldBeCalledTimes(1);
        $serviceProphecy->getTags()->willReturn([$tag]);
        /* @var ServiceInterface $service */
        $service = $serviceProphecy->reveal();

        $decoration = new Decoration($service, 'foo', null);

        $this->assertEquals('bar', $decoration->getName());
        $this->assertEquals('App\Dummy', $decoration->getClass());
        $this->assertSame([$arg], $decoration->getArguments());
        $this->assertSame([$type], $decoration->getAutowiringTypes());
        $this->assertSame([$tag], $decoration->getTags());
        $this->assertEquals('foo', $decoration->getDecorates());
        $this->assertEquals('bar.inner', $decoration->getDecorationInnerName());
    }

    public function testConstructWithDecorationInnerName()
    {
        $arg = new \stdClass();
        $type = new \stdClass();
        $tag = new \stdClass();

        $serviceProphecy = $this->prophesize(ServiceInterface::class);
        $serviceProphecy->getName()->shouldBeCalledTimes(1);
        $serviceProphecy->getName()->willReturn('bar');
        $serviceProphecy->getClass()->shouldBeCalledTimes(1);
        $serviceProphecy->getClass()->willReturn('App\Dummy');
        $serviceProphecy->getArguments()->shouldBeCalledTimes(1);
        $serviceProphecy->getArguments()->willReturn([$arg]);
        $serviceProphecy->getAutowiringTypes()->shouldBeCalledTimes(1);
        $serviceProphecy->getAutowiringTypes()->willReturn([$type]);
        $serviceProphecy->getTags()->shouldBeCalledTimes(1);
        $serviceProphecy->getTags()->willReturn([$tag]);
        /* @var ServiceInterface $service */
        $service = $serviceProphecy->reveal();

        $decoration = new Decoration($service, 'foo', 'booze');

        $this->assertEquals('bar', $decoration->getName());
        $this->assertEquals('App\Dummy', $decoration->getClass());
        $this->assertSame([$arg], $decoration->getArguments());
        $this->assertSame([$type], $decoration->getAutowiringTypes());
        $this->assertSame([$tag], $decoration->getTags());
        $this->assertEquals('foo', $decoration->getDecorates());
        $this->assertEquals('booze', $decoration->getDecorationInnerName());
    }
}

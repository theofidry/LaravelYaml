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

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\FactoryService;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Definition\FactoryService
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FactoryServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $serviceProphecy = $this->prophesize(ServiceInterface::class);
        $serviceProphecy->getName()->shouldBeCalledTimes(2);
        $serviceProphecy->getName()->willReturn('serviceId');
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

        $factory = new FactoryService($service, 'factoryClass', 'create');

        $this->assertEquals('serviceId', $factory->getName());
        $this->assertEquals('App\Dummy', $factory->getClass());
        $this->assertEquals([], $factory->getArguments());
        $this->assertEquals([], $factory->getAutowiringTypes());
        $this->assertEquals([], $factory->getTags());
        $this->assertEquals(['factoryClass', 'create'], $factory->getFactory());

        $reference = new Reference('foo', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE);
        $factory = new FactoryService($service, $reference, 'create');

        $this->assertEquals('serviceId', $factory->getName());
        $this->assertEquals('App\Dummy', $factory->getClass());
        $this->assertEquals([], $factory->getArguments());
        $this->assertEquals([], $factory->getAutowiringTypes());
        $this->assertEquals([], $factory->getTags());
        $this->assertEquals([$reference, 'create'], $factory->getFactory());
    }
}

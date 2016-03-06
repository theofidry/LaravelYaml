<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\DependencyInjection\Resolver;

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Resolver\BaseReferenceResolver;
use Illuminate\Contracts\Foundation\Application;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Resolver\BaseReferenceResolver
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class BaseReferenceResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BaseReferenceResolver
     */
    private $resolver;

    public function setUp()
    {
        $this->resolver = new BaseReferenceResolver();
    }

    public function testResolveReference()
    {
        $dummy = new \stdClass();
        $reference = new Reference('dummy', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE);

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make('dummy')->shouldBeCalledTimes(1);
        $applicationProphecy->make('dummy')->willReturn($dummy);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $actual = $this->resolver->resolve($reference, $application);

        $this->assertSame($dummy, $actual);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\ServiceNotFoundException
     */
    public function testResolveInvalidReference()
    {
        $reference = new Reference('dummy', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE);

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make('dummy')->willThrow(\Exception::class);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $this->resolver->resolve($reference, $application);
    }

    public function testResolveIgnoreInvalidReference()
    {
        $reference = new Reference('dummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE);

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make('dummy')->willThrow(\Exception::class);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $actual = $this->resolver->resolve($reference, $application);

        $this->assertNull($actual);
    }

    public function testResolveNullOnInvalidReference()
    {
        $reference = new Reference('dummy', BuilderInterface::NULL_ON_INVALID_REFERENCE);

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make('dummy')->willThrow(\Exception::class);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $actual = $this->resolver->resolve($reference, $application);

        $this->assertNull($actual);
    }
}

<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\DependencyInjection\Builder\Instantiator;

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Builder\Instantiator\ServiceInstantiator;
use Fidry\LaravelYaml\DependencyInjection\Definition\FactoryInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ParameterResolverInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ReferenceResolverInterface;
use Fidry\LaravelYaml\Test\DummyInterface;
use Fidry\LaravelYaml\Test\DummyService;
use Fidry\LaravelYaml\Test\DummyStaticFactory;
use Fidry\LaravelYaml\Test\Foundation\ApplicationMock;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Builder\Instantiator\ServiceInstantiator
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ServiceInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildService()
    {
        $dummyProphecy = $this->prophesize(ServiceInterface::class);
        $dummyProphecy->getClass()->shouldBeCalledTimes(1);
        $dummyProphecy->getClass()->willReturn(DummyService::class);
        $dummyProphecy->getArguments()->shouldBeCalledTimes(1);
        $dummyProphecy->getArguments()->willReturn([]);
        /* @var ServiceInterface $dummy */
        $dummy = $dummyProphecy->reveal();

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve(Argument::cetera())->shouldNotBeCalled();
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance(Argument::cetera())->shouldNotBeCalled();
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $instantiator = new ServiceInstantiator($parameterResolver, $referenceResolver, $application);

        $instantiator->create($dummy);

        $this->assertTrue(true);
    }

    public function testBuildServiceWithArguments()
    {
        $reference = new Reference('otherDummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE);

        $dummyProphecy = $this->prophesize(ServiceInterface::class);
        $dummyProphecy->getClass()->shouldBeCalledTimes(1);
        $dummyProphecy->getClass()->willReturn(DummyService::class);
        $dummyProphecy->getArguments()->shouldBeCalledTimes(1);
        $dummyProphecy->getArguments()->willReturn([
            '%foo%',
            $reference,
        ]);
        /* @var ServiceInterface $dummy */
        $dummy = $dummyProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance(Argument::cetera())->shouldNotBeCalled();
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve('%foo%')->shouldBeCalledTimes(1);
        $parameterResolverProphecy->resolve('%foo%')->willReturn('bar');
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve($reference, $application)->shouldBeCalledTimes(1);
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $instantiator = new ServiceInstantiator($parameterResolver, $referenceResolver, $application);

        $instantiator->create($dummy);

        $this->assertTrue(true);
    }

    public function testFactoryService()
    {
        $dummyProphecy = $this->prophesize(FactoryInterface::class);
        $dummyProphecy->getArguments()->shouldBeCalledTimes(1);
        $dummyProphecy->getArguments()->willReturn([]);
        $dummyProphecy->getFactory()->shouldBeCalledTimes(2);
        $dummyProphecy->getFactory()->willReturn([
            DummyStaticFactory::class,
            'create',
        ]);
        /* @var FactoryInterface $dummy */
        $dummy = $dummyProphecy->reveal();

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(DummyStaticFactory::class)->shouldBeCalledTimes(1);
        $parameterResolverProphecy->resolve(DummyStaticFactory::class)->willReturn(DummyStaticFactory::class);
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve(Argument::cetera())->shouldNotBeCalled();
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance(Argument::cetera())->shouldNotBeCalled();
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $instantiator = new ServiceInstantiator($parameterResolver, $referenceResolver, $application);

        $instantiator->create($dummy);

        $this->assertTrue(true);
    }

    public function testBuildFactoryWithArguments()
    {
        $reference = new Reference('otherDummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE);

        $dummyProphecy = $this->prophesize(FactoryInterface::class);
        $dummyProphecy->getArguments()->shouldBeCalledTimes(1);
        $dummyProphecy->getArguments()->willReturn([
            '%foo%',
            $reference,
        ]);
        $dummyProphecy->getFactory()->shouldBeCalledTimes(2);
        $dummyProphecy->getFactory()->willReturn([
            DummyStaticFactory::class,
            'create',
        ]);
        /* @var FactoryInterface $dummy */
        $dummy = $dummyProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance(Argument::cetera())->shouldNotBeCalled();
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(DummyStaticFactory::class)->shouldBeCalledTimes(1);
        $parameterResolverProphecy->resolve(DummyStaticFactory::class)->willReturn(DummyStaticFactory::class);
        $parameterResolverProphecy->resolve('%foo%')->shouldBeCalledTimes(1);
        $parameterResolverProphecy->resolve('%foo%')->willReturn('bar');
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve($reference, $application)->shouldBeCalledTimes(1);
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $instantiator = new ServiceInstantiator($parameterResolver, $referenceResolver, $application);

        $instantiator->create($dummy);

        $this->assertTrue(true);
    }
}

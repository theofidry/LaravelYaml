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

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Builder\ServicesBuilder;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
use Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\Exception as ResolverException;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ParameterResolverInterface;
use Fidry\LaravelYaml\DependencyInjection\Resolver\ReferenceResolverInterface;
use Fidry\LaravelYaml\Test\DummyInterface;
use Fidry\LaravelYaml\Test\DummyService;
use Fidry\LaravelYaml\Test\Foundation\ApplicationMock;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Builder\ServicesBuilder
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ServicesBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyBuild()
    {
        $services = [];
        $parameters = [];

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve(Argument::cetera())->shouldNotBeCalled();
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Argument::cetera())->shouldNotBeCalled();
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $application = new ApplicationMock($application);

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }

    public function testBuildService()
    {
        $services = [
            new Service('dummy', DummyService::class),
        ];
        $parameters = [];

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve(Argument::cetera())->shouldNotBeCalled();
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance('dummy', Argument::type(DummyService::class))->shouldBeCalledTimes(1);
        $applicationProphecy->bind(DummyService::class, 'dummy', false)->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $application = new ApplicationMock($application);

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }

    public function testBuildServiceWithArguments()
    {
        $reference = new Reference('otherDummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE);
        $services = [
            new Service(
                'dummy',
                DummyService::class,
                [
                    '%foo%',
                    $reference,
                ]
            ),
        ];
        $parameters = [
            'foo' => 'bar'
        ];

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance('dummy', Argument::type(DummyService::class))->shouldBeCalledTimes(1);
        $applicationProphecy->bind(DummyService::class, 'dummy', false)->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $application = new ApplicationMock($application);

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve('%foo%')->shouldBeCalledTimes(1);
        $parameterResolverProphecy->resolve('%foo%')->willReturn('bar');
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve($reference, $application)->shouldBeCalledTimes(1);
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }

    public function testBuildAutowiredService()
    {
        $services = [
            new Service('dummy', DummyService::class, [], [DummyInterface::class]),
        ];
        $parameters = [];

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve(Argument::cetera())->shouldNotBeCalled();
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance('dummy', Argument::type(DummyService::class))->shouldBeCalledTimes(1);
        $applicationProphecy->bind(DummyService::class, 'dummy', false)->shouldBeCalledTimes(1);
        $applicationProphecy->bind(DummyInterface::class, 'dummy', false)->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $application = new ApplicationMock($application);

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }

    public function testBuildTaggedService()
    {
        $services = [
            new Service('dummy', DummyService::class, [], [], [['dummyTag' => null]]),
        ];
        $parameters = [];

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve(Argument::cetera())->shouldNotBeCalled();
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance('dummy', Argument::type(DummyService::class))->shouldBeCalledTimes(1);
        $applicationProphecy->bind(DummyService::class, 'dummy', false)->shouldBeCalledTimes(1);
        $applicationProphecy->tag('dummy', ['dummyTag'])->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $application = new ApplicationMock($application);

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\DependencyInjection\Exception
     */
    public function testBuildWithBindingError()
    {
        $services = [
            new Service('dummy', DummyService::class),
        ];
        $parameters = [];

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ParameterResolverInterface $parameterResolver */
        $parameterResolver = $parameterResolverProphecy->reveal();

        $referenceResolverProphecy = $this->prophesize(ReferenceResolverInterface::class);
        $referenceResolverProphecy->resolve(Argument::cetera())->shouldNotBeCalled();
        /* @var ReferenceResolverInterface $referenceResolver */
        $referenceResolver = $referenceResolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->instance('dummy', Argument::type(DummyService::class))->shouldBeCalledTimes(1);
        $applicationProphecy->bind(DummyService::class, 'dummy', false)->willThrow(BindingResolutionException::class);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $application = new ApplicationMock($application);

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\DependencyInjection\Exception
     */
    public function testBuildWithResolverError()
    {
        $services = [
            new Service('dummy', DummyService::class, ['%foo%']),
        ];
        $parameters = [];

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve('%foo%')->shouldBeCalledTimes(1);
        $parameterResolverProphecy->resolve('%foo%')->willThrow(ResolverException::class);
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
        $application = new ApplicationMock($application);

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\DependencyInjection\Exception
     */
    public function testBuildWithUnexpectedError()
    {
        $services = [
            new Service('dummy', DummyService::class, ['%foo%']),
        ];
        $parameters = [];

        $parameterResolverProphecy = $this->prophesize(ParameterResolverInterface::class);
        $parameterResolverProphecy->resolve('%foo%')->shouldBeCalledTimes(1);
        $parameterResolverProphecy->resolve('%foo%')->willThrow(\Exception::class);
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
        $application = new ApplicationMock($application);

        $builder = new ServicesBuilder($services, $parameters, $parameterResolver, $referenceResolver);

        $builder->build($application);

        $this->assertTrue(true);
    }
}

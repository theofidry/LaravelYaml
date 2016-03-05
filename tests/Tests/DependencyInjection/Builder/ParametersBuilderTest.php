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

use Fidry\LaravelYaml\DependencyInjection\Builder\ParametersBuilder;
use Fidry\LaravelYaml\Test\Foundation\ApplicationMock;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Builder\ParametersBuilder
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ParametersBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideParameters
     */
    public function testBuildParameters($application, $aliases)
    {
        $builder = new ParametersBuilder($aliases);
        $builder->build($application);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideParametersError
     *
     * @expectedException \Fidry\LaravelYaml\Exception\DependencyInjection\Exception
     */
    public function testBuildParametersError($application, $aliases)
    {
        $builder = new ParametersBuilder($aliases);
        $builder->build($application);

        $this->assertTrue(true);
    }

    public function provideParameters()
    {
        $parameters = [];

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->shouldBeCalledTimes(1);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        yield [new ApplicationMock($application), $parameters];

        $parameters = [
            'foo' => 'bar',
        ];

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->shouldBeCalledTimes(1);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        $applicationProphecy->bind('foo', 'bar', Argument::any())->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        yield [new ApplicationMock($application), $parameters];
    }

    public function provideParametersError()
    {
        $parameters = [];

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willThrow(BindingResolutionException::class);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        yield [new ApplicationMock($application), $parameters];

        $parameters = [
            'foo' => '%bar%',
            'bar' => '%foo%',
        ];

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->shouldBeCalledTimes(1);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        yield [new ApplicationMock($application), $parameters];

        $parameters = [];

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willThrow(\Exception::class);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        yield [new ApplicationMock($application), $parameters];
    }
}

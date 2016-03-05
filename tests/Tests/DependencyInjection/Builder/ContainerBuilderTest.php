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
use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ContainerBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildContainer()
    {
        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->alias(Argument::cetera())->shouldNotBeCalled();
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

        $parametersBuilderProphecy = $this->prophesize(BuilderInterface::class);
        $parametersBuilderProphecy->build($application)->shouldBeCalledTimes(1);
        /* @var BuilderInterface $parametersBuilder */
        $parametersBuilder = $parametersBuilderProphecy->reveal();

        $aliasesBuilderProphecy = $this->prophesize(BuilderInterface::class);
        $aliasesBuilderProphecy->build($application)->shouldBeCalledTimes(1);
        /* @var BuilderInterface $aliasesBuilder */
        $aliasesBuilder = $aliasesBuilderProphecy->reveal();

        $servicesBuilderProphecy = $this->prophesize(BuilderInterface::class);
        $servicesBuilderProphecy->build($application)->shouldBeCalledTimes(1);
        /* @var BuilderInterface $servicesBuilder */
        $servicesBuilder = $servicesBuilderProphecy->reveal();

        $builder = new ContainerBuilder($parametersBuilder, $aliasesBuilder, $servicesBuilder);
        $builder->build($application);

        $this->assertTrue(true);
    }
}

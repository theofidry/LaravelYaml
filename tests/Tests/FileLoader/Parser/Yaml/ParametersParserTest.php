<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\FileLoader\Parser\Yaml;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\ParametersParser;
use Fidry\LaravelYaml\Test\Foundation\ApplicationMock;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\FileLoader\Parser\Yaml\ParametersParser
 *
 * @author             Théo FIDRY <theo.fidry@gmail.com>
 */
class ParametersParserTest extends \PHPUnit_Framework_TestCase
{
    const FILE_NAME = 'dummy.yml';

    /**
     * @dataProvider provideUnknownContent
     */
    public function testParseUnknowContent($content)
    {
        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $applicationMock = new ApplicationMock($application);

        $builder = new ContainerBuilder();

        $parser = new ParametersParser($resolver);
        $parser->parse($builder, $content, self::FILE_NAME);

        $builder->build($applicationMock);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideInvalidContent
     *
     * @expectedException \Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException
     */
    public function testParseInvalidContent($content)
    {
        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve(Argument::any())->shouldNotBeCalled();
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $builder = new ContainerBuilder();

        $parser = new ParametersParser($resolver);
        $parser->parse($builder, $content, self::FILE_NAME);
    }

    public function testParseValidContent()
    {
        $content = [
            'parameters' => [
                'foo' => 'bar',
            ]
        ];

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        $applicationProphecy->bind('foo', 'bar', Argument::any())->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $applicationMock = new ApplicationMock($application);

        $builder = new ContainerBuilder();

        $parser = new ParametersParser($resolver);
        $parser->parse($builder, $content, self::FILE_NAME);

        $builder->build($applicationMock);

        $this->assertTrue(true);
    }

    public function provideUnknownContent()
    {
        yield [[]];
        yield [null];
        yield [['invalidKey' => null]];
        yield [['parameters' => null]];
    }

    public function provideInvalidContent()
    {
        yield [['parameters' => 'something']];
        yield [['parameters' => true]];
    }
}



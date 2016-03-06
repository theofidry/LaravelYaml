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
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\DefinitionsParser;
use Fidry\LaravelYaml\Test\Foundation\ApplicationMock;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\FileLoader\Parser\Yaml\DefinitionsParser
 *
 * @author             Théo FIDRY <theo.fidry@gmail.com>
 */
class DefinitionsParserTest extends \PHPUnit_Framework_TestCase
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

        $parser = new DefinitionsParser($resolver);
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

        $parser = new DefinitionsParser($resolver);
        $parser->parse($builder, $content, self::FILE_NAME);
    }

    public function testParseAliasContent()
    {
        $content = [
            'services' => [
                'foo' => '@bar',
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
        $applicationProphecy->alias('bar', 'foo')->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $applicationMock = new ApplicationMock($application);

        $builder = new ContainerBuilder();

        $parser = new DefinitionsParser($resolver);
        $parser->parse($builder, $content, self::FILE_NAME);

        $builder->build($applicationMock);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider provideValidContent
     */
    public function testParseValidContent($content, $resolver, $application)
    {
        $builder = new ContainerBuilder();

        $parser = new DefinitionsParser($resolver);
        $parser->parse($builder, $content, self::FILE_NAME);

        $builder->build($application);

        $this->assertTrue(true);
    }

    public function provideUnknownContent()
    {
        yield [[]];
        yield [null];
        yield [['invalidKey' => null]];
        yield [['services' => null]];
    }

    public function provideInvalidContent()
    {
        yield [['services' => 'something']];
        yield [['services' => true]];
        yield [
            [
                'services' => [
                    'foo' => 'bar', // alias must be a reference (@something)
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        // lacks "class"
                        'alias' => 'bar',
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => 'App\Dummy',
                        'alias' => ['not string'],  // alias must be a string
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => ['not string'],  // class must be a string
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => 'App\Dummy',
                        'tags' => true, // tags must be an array
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => 'App\Dummy',
                        'tags' => [
                            'not array',    // value must be array
                        ],
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => 'App\Dummy',
                        'tags' => [
                            [
                                'foo' => 'bar'  // do not have "name" key
                            ]
                        ],
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => 'App\Dummy',
                        'tags' => [
                            [
                                'name' => 'foo',
                                'attribute' => ['not scalar'],
                            ]
                        ],
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => 'App\Dummy',
                        'autowiringTypes' => 'not array',
                    ],
                ],
            ]
        ];
        yield [
            [
                'services' => [
                    'foo' => [
                        'class' => 'App\Dummy',
                        'autowiringTypes' => [
                            ['not string'],
                        ],
                    ],
                ],
            ]
        ];
    }

    public function provideValidContent()
    {
        $content = [
            'services' => [
                'foo' => [
                    'class' => 'App\Dummy',
                    'alias' => 'bar',
                ],
            ]
        ];

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        $applicationProphecy->singleton('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->bind(Argument::cetera())->shouldBeCalledTimes(1);
        $applicationProphecy->tag('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->alias('bar', 'foo')->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $applicationMock = new ApplicationMock($application);

        yield [$content, $resolver, $applicationMock];

        $content = [
            'services' => [
                'foo' => [
                    'class' => 'App\Dummy',
                    'alias' => 'bar',
                    'arguments' => [],
                ],
            ]
        ];

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        $resolverProphecy->resolve([])->willReturn([]);
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        $applicationProphecy->singleton('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->bind(Argument::cetera())->shouldBeCalledTimes(1);
        $applicationProphecy->tag('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->alias('bar', 'foo')->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $applicationMock = new ApplicationMock($application);

        yield [$content, $resolver, $applicationMock];

        $content = [
            'services' => [
                'foo' => [
                    'class' => 'App\Dummy',
                    'tags' => [
                        [
                            'name' => 'foo',
                        ]
                    ],
                ],
            ]
        ];

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        $resolverProphecy->resolve([])->willReturn([]);
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        $applicationProphecy->singleton('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->bind(Argument::cetera())->shouldBeCalledTimes(1);
        $applicationProphecy->tag('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->alias('bar', 'foo')->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $applicationMock = new ApplicationMock($application);

        yield [$content, $resolver, $applicationMock];

        $content = [
            'services' => [
                'foo' => [
                    'class' => 'App\Dummy',
                    'autowiringTypes' => [
                        'App\DummyInterface',
                    ],
                ],
            ]
        ];

        $configRepositoryProphecy = $this->prophesize(Repository::class);
        $configRepositoryProphecy->get(Argument::any())->shouldNotBeCalled();
        /* @var Repository $configRepository */
        $configRepository = $configRepositoryProphecy->reveal();

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        $resolverProphecy->resolve([])->willReturn([]);
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Repository::class, [])->willReturn($configRepository);
        $applicationProphecy->singleton('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->bind(Argument::cetera())->shouldBeCalledTimes(1);
        $applicationProphecy->tag('foo', Argument::any())->shouldBeCalledTimes(1);
        $applicationProphecy->alias('bar', 'foo')->shouldBeCalledTimes(1);
        /* @var Application $application */
        $application = $applicationProphecy->reveal();
        $applicationMock = new ApplicationMock($application);

        yield [$content, $resolver, $applicationMock];
    }
}

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

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Fidry\LaravelYaml\DependencyInjection\Definition\FactoryService;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\DefinitionsParser;
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

        $builderProphecy = $this->prophesize(BuilderInterface::class);
        $builderProphecy->build(Argument::any())->shouldNotBeCalled();
        /* @var BuilderInterface $builder */
        $builder = $builderProphecy->reveal();

        $containerBuilderReflectionClass = new \ReflectionClass(ContainerBuilder::class);
        /* @var ContainerBuilder $containerBuilder */
        $containerBuilder = $containerBuilderReflectionClass->newInstanceArgs([$builder, $builder, $builder]);

        $parser = new DefinitionsParser($resolver);
        $parser->parse($containerBuilder, $content, self::FILE_NAME);

        $containerBuilderReflectionObject = new \ReflectionObject($containerBuilder);
        $parameters = $containerBuilderReflectionObject->getProperty('parameters');
        $parameters->setAccessible(true);
        $actualParameters = $parameters->getValue($containerBuilder);
        $aliases = $containerBuilderReflectionObject->getProperty('aliases');
        $aliases->setAccessible(true);
        $actualAliases = $aliases->getValue($containerBuilder);
        $services = $containerBuilderReflectionObject->getProperty('services');
        $services->setAccessible(true);
        $actualServices = $services->getValue($containerBuilder);

        $this->assertCount(0, $actualParameters);
        $this->assertCount(0, $actualAliases);
        $this->assertCount(0, $actualServices);
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

        $builderProphecy = $this->prophesize(BuilderInterface::class);
        $builderProphecy->build(Argument::any())->shouldNotBeCalled();
        /* @var BuilderInterface $builder */
        $builder = $builderProphecy->reveal();

        $containerBuilderReflectionClass = new \ReflectionClass(ContainerBuilder::class);
        /* @var ContainerBuilder $containerBuilder */
        $containerBuilder = $containerBuilderReflectionClass->newInstanceArgs([$builder, $builder, $builder]);

        $parser = new DefinitionsParser($resolver);
        $parser->parse($containerBuilder, $content, self::FILE_NAME);

        $containerBuilderReflectionObject = new \ReflectionObject($containerBuilder);
        $parameters = $containerBuilderReflectionObject->getProperty('parameters');
        $parameters->setAccessible(true);
        $actualParameters = $parameters->getValue($containerBuilder);
        $aliases = $containerBuilderReflectionObject->getProperty('aliases');
        $aliases->setAccessible(true);
        $actualAliases = $aliases->getValue($containerBuilder);
        $services = $containerBuilderReflectionObject->getProperty('services');
        $services->setAccessible(true);
        $actualServices = $services->getValue($containerBuilder);

        $this->assertCount(0, $actualParameters);
        $this->assertEquals(
            [
                'foo' => new Alias('foo', 'bar'),
            ],
            $actualAliases
        );
        $this->assertCount(0, $actualServices);
    }

    /**
     * @dataProvider provideValidContent
     */
    public function testParseValidContent($content, $resolver, $expectedAliases, $expectedServices)
    {
        $builderProphecy = $this->prophesize(BuilderInterface::class);
        $builderProphecy->build(Argument::any())->shouldNotBeCalled();
        /* @var BuilderInterface $builder */
        $builder = $builderProphecy->reveal();

        $containerBuilderReflectionClass = new \ReflectionClass(ContainerBuilder::class);
        /* @var ContainerBuilder $containerBuilder */
        $containerBuilder = $containerBuilderReflectionClass->newInstanceArgs([$builder, $builder, $builder]);

        $parser = new DefinitionsParser($resolver);
        $parser->parse($containerBuilder, $content, self::FILE_NAME);

        $containerBuilderReflectionObject = new \ReflectionObject($containerBuilder);
        $parameters = $containerBuilderReflectionObject->getProperty('parameters');
        $parameters->setAccessible(true);
        $actualParameters = $parameters->getValue($containerBuilder);
        $aliases = $containerBuilderReflectionObject->getProperty('aliases');
        $aliases->setAccessible(true);
        $actualAliases = $aliases->getValue($containerBuilder);
        $services = $containerBuilderReflectionObject->getProperty('services');
        $services->setAccessible(true);
        $actualServices = $services->getValue($containerBuilder);

        $this->assertCount(0, $actualParameters);
        $this->assertEquals($expectedAliases, $actualAliases);
        $this->assertEquals($expectedServices, $actualServices);
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
                'don' => '@foo',
            ]
        ];

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $aliases = [
            'bar' => new Alias('bar', 'foo'),
            'don' => new Alias('don', 'foo'),
        ];
        $services = [
            'foo' => new Service('foo', 'App\Dummy'),
        ];

        yield [$content, $resolver, $aliases, $services];

        $content = [
            'services' => [
                'foo' => [
                    'class' => 'App\Dummy',
                    'alias' => 'bar',
                    'arguments' => [
                        '%default.val%',
                        '@dummy',
                    ],
                ],
                'donjo' => [
                    'class' => 'App\AnotherDummy',
                    'arguments' => [],
                ],
            ]
        ];

        $reference = new Reference('dummy', ContainerBuilder::EXCEPTION_ON_INVALID_REFERENCE);

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        $resolverProphecy->resolve([])->willReturn([]);
        $resolverProphecy->resolve('%default.val%')->shouldBeCalled();
        $resolverProphecy->resolve('@dummy')->shouldBeCalled();
        $resolverProphecy->resolve(['%default.val%', '@dummy'])->willReturn(['%default.val%', $reference]);
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $aliases = [
            'bar' => new Alias('bar', 'foo'),
        ];
        $services = [
            'foo' => new Service(
                'foo',
                'App\Dummy',
                [
                    '%default.val%',
                    $reference,
                ]
            ),
            'donjo' => new Service(
                'donjo',
                'App\AnotherDummy',
                []
            ),
        ];

        yield [$content, $resolver, $aliases, $services];

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

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        $resolverProphecy->resolve([])->willReturn([]);
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $aliases = [];
        $services = [
            'foo' => new Service('foo', 'App\Dummy', [], [], ['foo' => []]),
        ];

        yield [$content, $resolver, $aliases, $services];

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

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        $resolverProphecy->resolve([])->willReturn([]);
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $aliases = [];
        $services = [
            'foo' => new Service('foo', 'App\Dummy', [], ['App\DummyInterface'], []),
        ];

        yield [$content, $resolver, $aliases, $services];

        $content = [
            'services' => [
                'foo' => [
                    'class' => 'App\Dummy',
                    'factory' => [
                        'App\DummyFactory',
                        'create',
                    ],
                ],
                'bar' => [
                    'class' => 'App\Dummy',
                    'factory' => [
                        '@foo',
                        'create',
                    ],
                ],
            ]
        ];

        $resolverProphecy = $this->prophesize(ResolverInterface::class);
        $resolverProphecy->resolve('bar')->willReturn('bar');
        $resolverProphecy->resolve([])->willReturn([]);
        $resolverProphecy->resolve('App\DummyFactory')->willReturn('App\DummyFactory');
        $resolverProphecy->resolve('@foo')->willReturn(new Reference('foo', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE));
        /* @var ResolverInterface $resolver */
        $resolver = $resolverProphecy->reveal();

        $aliases = [];
        $services = [
            'foo' => new FactoryService(
                new Service('foo', 'App\Dummy', [], [], []),
                'App\DummyFactory',
                'create'
            ),
            'bar' => new FactoryService(
                new Service('bar', 'App\Dummy', [], [], []),
                new Reference('foo', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE),
                'create'
            ),
        ];

        yield [$content, $resolver, $aliases, $services];
    }
}

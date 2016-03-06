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
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\ImportsParser;
use Fidry\LaravelYaml\FileLoader\Parser\Yaml\ParametersParser;
use Fidry\LaravelYaml\Test\Foundation\ApplicationMock;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\FileLoader\Parser\Yaml\ImportsParser
 *
 * @author             Théo FIDRY <theo.fidry@gmail.com>
 */
class ImportsParserTest extends \PHPUnit_Framework_TestCase
{
    const FILE_NAME = 'dummy.yml';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ContainerBuilder
     */
    private $builder;

    /**
     * @var ImportsParser
     */
    private $parser;

    public function setUp()
    {
        $applicationProphecy = $this->prophesize(Application::class);
        $applicationProphecy->make(Argument::cetera())->shouldNotBeCalled();
        /* @var Application $application */
        $application = $applicationProphecy->reveal();

//        $builderProphecy

        $this->application = new ApplicationMock($application);
        $this->builder = new ContainerBuilder();
        $this->parser = new ImportsParser();
    }

    /**
     * @dataProvider provideUnknownContent
     */
    public function testParseUnknowContent($content)
    {
        $actual = $this->parser->parse($this->builder, $content, self::FILE_NAME);
        $this->assertEquals([], $actual);
    }

    /**
     * @dataProvider provideInvalidContent
     *
     * @expectedException \Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException
     */
    public function testParseInvalidContent($content)
    {
        $this->parser->parse($this->builder, $content, self::FILE_NAME);
    }

    /**
     * @dataProvider provideValidContent
     */
    public function testParseValidContent($content, $expected)
    {
        $actual = $this->parser->parse($this->builder, $content, self::FILE_NAME);

        $this->assertEquals($expected, $actual);
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
        yield [['imports' => 'something']];
        yield [['imports' => true]];
    }

    public function provideValidContent()
    {
        yield [
            [],
            [],
        ];
        yield [
            [
                'imports' => [],
            ],
            []
        ];
        yield [
            [
                'imports' => [
                    [
                        'resource' => 'foo.yml',
                    ],
                ]
            ],
            [
                'foo.yml',
            ],
        ];
        yield [
            [
                'imports' => [
                    [
                        'resource' => 'foo.yml',
                    ],
                    [
                        'resource' => 'bar.yml',
                        'dummy' => 'hello'
                    ],
                ],
            ],
            [
                'foo.yml',
                'bar.yml',
            ],
        ];
    }
}

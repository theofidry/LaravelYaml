<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\FileLoader\Yaml;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\FileLoader\Parser\ParserInterface;
use Fidry\LaravelYaml\FileLoader\Yaml\YamlFileLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Prophecy\Argument;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * @covers Fidry\LaravelYaml\FileLoader\Yaml\YamlFileLoader
 * @covers Fidry\LaravelYaml\FileLoader\Yaml\YamlSingleFileLoader
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    const ROOT_DIR = 'ROOT_DIR';

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        vfsStreamWrapper::register();
        $this->root = vfsStream::newDirectory(self::ROOT_DIR);
        vfsStreamWrapper::setRoot($this->root);
    }

    public function testConstruct()
    {
        $fileLocatorProphecy = $this->prophesize(FileLocatorInterface::class);
        $fileLocatorProphecy->locate(Argument::cetera())->shouldNotBeCalled();
        /* @var FileLocatorInterface $fileLocator */
        $fileLocator = $fileLocatorProphecy->reveal();

        new YamlFileLoader(
            new ContainerBuilder(),
            $fileLocator
        );

        $this->assertTrue(true);
    }

    public function testParseResource()
    {
        $resource = 'dummy.yml';
        $yamlFileContent = 'yaml file content';
        $yamlParsed = [
            'parameters' => [],
        ];

        $this->root->addChild(
            vfsStream::newFile($resource)
            ->withContent($yamlFileContent)
        );
        $file = vfsStream::url(sprintf('%s/%s', self::ROOT_DIR, $resource));

        $fileLocatorProphecy = $this->prophesize(FileLocatorInterface::class);
        $fileLocatorProphecy->locate($resource, null, true)->shouldBeCalledTimes(1);
        $fileLocatorProphecy
            ->locate($resource, null, true)
            ->willReturn($file)
        ;
        /* @var FileLocatorInterface $fileLocator */
        $fileLocator = $fileLocatorProphecy->reveal();

        $yamlParserProphecy = $this->prophesize(Parser::class);
        $yamlParserProphecy->parse($yamlFileContent)->shouldBeCalledTimes(1);
        $yamlParserProphecy->parse($yamlFileContent)->willReturn($yamlParsed);
        /* @var Parser $yamlParser */
        $yamlParser = $yamlParserProphecy->reveal();

        $builder = new ContainerBuilder();

        $parametersParserProphecy = $this->prophesize(ParserInterface::class);
        $parametersParserProphecy
            ->parse($builder, $yamlParsed, $resource)
            ->shouldBeCalledTimes(1)
        ;
        /* @var ParserInterface $parametersParser */
        $parametersParser = $parametersParserProphecy->reveal();

        $definitionsParserProphecy = $this->prophesize(ParserInterface::class);
        $definitionsParserProphecy
            ->parse($builder, $yamlParsed, $resource)
            ->shouldBeCalledTimes(1)
        ;
        /* @var ParserInterface $parametersParser */
        $definitionsParser = $definitionsParserProphecy->reveal();

        $loader = new YamlFileLoader(
            $builder,
            $fileLocator,
            $parametersParser,
            $definitionsParser,
            null,
            $yamlParser
        );

        $self = $loader->load($resource);

        $this->assertSame($loader, $self);
    }

    public function testParseResourceWithInclude()
    {
        $resource = 'dummy.yml';
        $importedResource = 'foo.yml';
        $yamlFileContent = 'yaml file content';
        $yamlParsed = [
            'imports' => [
                ['resource' => $importedResource],
            ],
        ];

        $this->root->addChild(
            vfsStream
                ::newFile($resource)
                ->withContent($yamlFileContent)
        );
        $file = vfsStream::url(sprintf('%s/%s', self::ROOT_DIR, $resource));
        $this->root->addChild(
            vfsStream
                ::newFile($importedResource)
                ->withContent('')
        );
        $importedfile = vfsStream::url(sprintf('%s/%s', self::ROOT_DIR, $importedResource));

        $fileLocatorProphecy = $this->prophesize(FileLocatorInterface::class);
        $fileLocatorProphecy->locate($resource, null, true)->shouldBeCalledTimes(1);
        $fileLocatorProphecy
            ->locate($resource, null, true)
            ->willReturn($file)
        ;
        $fileLocatorProphecy
            ->locate($importedResource, null, true)
            ->willReturn($importedfile)
        ;
        /* @var FileLocatorInterface $fileLocator */
        $fileLocator = $fileLocatorProphecy->reveal();

        $yamlParserProphecy = $this->prophesize(Parser::class);
        $yamlParserProphecy->parse($yamlFileContent)->shouldBeCalledTimes(1);
        $yamlParserProphecy->parse($yamlFileContent)->willReturn($yamlParsed);
        $yamlParserProphecy->parse('')->shouldBeCalledTimes(1);
        $yamlParserProphecy->parse('')->willReturn([]);
        /* @var Parser $yamlParser */
        $yamlParser = $yamlParserProphecy->reveal();

        $builder = new ContainerBuilder();

        $parametersParserProphecy = $this->prophesize(ParserInterface::class);
        $parametersParserProphecy
            ->parse($builder, $yamlParsed, $resource)
            ->shouldBeCalledTimes(1)
        ;
        $parametersParserProphecy
            ->parse($builder, [], $importedResource)
            ->shouldBeCalledTimes(1)
        ;
        /* @var ParserInterface $parametersParser */
        $parametersParser = $parametersParserProphecy->reveal();

        $definitionsParserProphecy = $this->prophesize(ParserInterface::class);
        $definitionsParserProphecy
            ->parse($builder, $yamlParsed, $resource)
            ->shouldBeCalledTimes(1)
        ;
        $definitionsParserProphecy
            ->parse($builder, [], $importedResource)
            ->shouldBeCalledTimes(1)
        ;
        /* @var ParserInterface $parametersParser */
        $definitionsParser = $definitionsParserProphecy->reveal();

        $importsParserProphecy = $this->prophesize(ParserInterface::class);
        $importsParserProphecy
            ->parse($builder, $yamlParsed, $resource)
            ->shouldBeCalledTimes(1)
        ;
        $importsParserProphecy
            ->parse($builder, $yamlParsed, $resource)
            ->willReturn([$importedResource])
        ;
        $importsParserProphecy
            ->parse($builder, [], $importedResource)
            ->shouldBeCalledTimes(1)
        ;
        $importsParserProphecy
            ->parse($builder, [], $importedResource)
            ->willReturn([])
        ;
        /* @var ParserInterface $importsParser */
        $importsParser = $importsParserProphecy->reveal();

        $loader = new YamlFileLoader(
            $builder,
            $fileLocator,
            $parametersParser,
            $definitionsParser,
            $importsParser,
            $yamlParser
        );

        $self = $loader->load($resource);

        $this->assertSame($loader, $self);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException
     */
    public function testParseUnparsableResource()
    {
        $resource = 'dummy.yml';
        $yamlFileContent = 'yaml file content';

        $this->root->addChild(
            vfsStream::newFile($resource)
                ->withContent($yamlFileContent)
        );
        $file = vfsStream::url(sprintf('%s/%s', self::ROOT_DIR, $resource));

        $fileLocatorProphecy = $this->prophesize(FileLocatorInterface::class);
        $fileLocatorProphecy->locate($resource, null, true)->shouldBeCalledTimes(1);
        $fileLocatorProphecy
            ->locate($resource, null, true)
            ->willReturn($file)
        ;
        /* @var FileLocatorInterface $fileLocator */
        $fileLocator = $fileLocatorProphecy->reveal();

        $yamlParserProphecy = $this->prophesize(Parser::class);
        $yamlParserProphecy->parse($yamlFileContent)->shouldBeCalledTimes(1);
        $yamlParserProphecy->parse($yamlFileContent)->willThrow(ParseException::class);
        /* @var Parser $yamlParser */
        $yamlParser = $yamlParserProphecy->reveal();

        $builder = new ContainerBuilder();

        $parametersParserProphecy = $this->prophesize(ParserInterface::class);
        $parametersParserProphecy->parse(Argument::cetera())->shouldNotBeCalled();
        /* @var ParserInterface $parametersParser */
        $parametersParser = $parametersParserProphecy->reveal();

        $definitionsParserProphecy = $this->prophesize(ParserInterface::class);
        $definitionsParserProphecy->parse(Argument::cetera())->shouldNotBeCalled();
        /* @var ParserInterface $parametersParser */
        $definitionsParser = $definitionsParserProphecy->reveal();

        $loader = new YamlFileLoader(
            $builder,
            $fileLocator,
            $parametersParser,
            $definitionsParser,
            null,
            $yamlParser
        );

        $self = $loader->load($resource);

        $this->assertSame($loader, $self);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException
     */
    public function testParseInexistingResource()
    {
        $resource = 'dummy.yml';
        $yamlFileContent = 'yaml file content';

        $fileLocatorProphecy = $this->prophesize(FileLocatorInterface::class);
        $fileLocatorProphecy->locate($resource, null, true)->shouldBeCalledTimes(1);
        $fileLocatorProphecy
            ->locate($resource, null, true)
            ->willReturn(null)
        ;
        /* @var FileLocatorInterface $fileLocator */
        $fileLocator = $fileLocatorProphecy->reveal();

        $yamlParserProphecy = $this->prophesize(Parser::class);
        $yamlParserProphecy->parse(Argument::any())->shouldNotBeCalled();
        $yamlParserProphecy->parse($yamlFileContent)->willThrow(ParseException::class);
        /* @var Parser $yamlParser */
        $yamlParser = $yamlParserProphecy->reveal();

        $builder = new ContainerBuilder();

        $parametersParserProphecy = $this->prophesize(ParserInterface::class);
        $parametersParserProphecy->parse(Argument::cetera())->shouldNotBeCalled();
        /* @var ParserInterface $parametersParser */
        $parametersParser = $parametersParserProphecy->reveal();

        $definitionsParserProphecy = $this->prophesize(ParserInterface::class);
        $definitionsParserProphecy->parse(Argument::cetera())->shouldNotBeCalled();
        /* @var ParserInterface $parametersParser */
        $definitionsParser = $definitionsParserProphecy->reveal();

        $loader = new YamlFileLoader(
            $builder,
            $fileLocator,
            $parametersParser,
            $definitionsParser,
            null,
            $yamlParser
        );

        $self = $loader->load($resource);

        $this->assertSame($loader, $self);
    }
}

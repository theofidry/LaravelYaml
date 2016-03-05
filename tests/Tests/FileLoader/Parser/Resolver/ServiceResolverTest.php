<?php
/**
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\FileLoader\Parser\Resolver;

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ServiceResolver;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @covers Fidry\LaravelYaml\FileLoader\Parser\Resolver\ServiceResolver
 *
 * @author             Théo FIDRY <theo.fidry@gmail.com>
 */
class ServiceResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceResolver
     */
    private $resolver;

    public function setUp()
    {
        $this->resolver = new ServiceResolver();
    }

    /**
     * @dataProvider provideExpressions
     */
    public function testResolveExpression($expression, $expected)
    {
        $actual = $this->resolver->resolve($expression);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provideReferences
     */
    public function testResolveReference($value, $expected)
    {
        $actual = $this->resolver->resolve($value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provideUnresolvableValues
     */
    public function testResolveUnresolvableValue($value)
    {
        $actual = $this->resolver->resolve($value);
        $this->assertEquals($value, $actual);
    }

    /**
     * @dataProvider provideArrayValues
     */
    public function testResolveArrayValues($value, $expected)
    {
        $actual = $this->resolver->resolve($value);
        $this->assertEquals($expected, $actual);
    }

    public function provideExpressions()
    {
        yield ['@=something', new Expression('something')];
        yield ['@=service("config").get("default.locale")', new Expression('service("config").get("default.locale")')];
    }

    public function provideReferences()
    {
        yield ['@dummy', new Reference('dummy', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE)];
        yield ['@?dummy', new Reference('dummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE)];
        yield ['@@dummy', new Reference('@dummy', null)];
        yield ['@?@dummy', new Reference('@dummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE)];
    }

    public function provideUnresolvableValues()
    {
        return [
            ['string value'],
            [null],
            [true],
            [10],
            [-.75],
        ];
    }

    public function provideArrayValues()
    {
        yield [
            [
                '@=something',
                '@dummy',
                true,
                [
                    '@=service("config").get("default.locale")',
                    '@?dummy',
                    -.75,
                ]
            ],
            [
                new Expression('something'),
                new Reference('dummy', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE),
                true,
                [
                    new Expression('service("config").get("default.locale")'),
                    new Reference('dummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE),
                    -.75,
                ]
            ]
        ];
    }
}

<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\DependencyInjection\Resolver;

use Fidry\LaravelYaml\DependencyInjection\Resolver\BaseParametersResolver;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryInterface;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Resolver\BaseParametersResolver
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class BaseParametersResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigRepositoryInterface
     */
    private $config;

    public function setUp()
    {
        $configRepositoryProphecy = $this->prophesize(ConfigRepositoryInterface::class);
        $configRepositoryProphecy->has(Argument::any())->willReturn(false);
        $this->config = $configRepositoryProphecy->reveal();
    }

    /**
     * @dataProvider provideParameters
     */
    public function testResolveParameters($config, $parameters, $expected)
    {
        $resolver = new BaseParametersResolver($config);
        $actual = $resolver->resolve($parameters);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\DependencyInjection\Resolver\ParameterCircularReferenceException
     */
    public function testResolveParametersWithLoop()
    {
        $resolver = new BaseParametersResolver($this->config);
        $resolver->resolve([
            'foo' => '%bar%',
            'bar' => '%foo%',
        ]);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\ParameterNotFoundException
     */
    public function testResolveParametersWithUnexistingParameter()
    {
        $resolver = new BaseParametersResolver($this->config);
        $resolver->resolve([
            'foo' => '%hello.world%',
        ]);
    }

    public function provideParameters()
    {
        $configRepositoryProphecy = $this->prophesize(ConfigRepositoryInterface::class);
        $configRepositoryProphecy->has('locale.default')->willReturn(true);
        $configRepositoryProphecy->get('locale.default')->willReturn('en-GB');
        $configRepositoryProphecy->has(Argument::any())->willReturn(false);

        yield [
            $configRepositoryProphecy->reveal(),
            [
                'boolParam' => true,
                'intParam' => 2000,
                'floatParam' => -.89,
                'objectParam' => new \stdClass(),
                'closureParam' => function () {},
                'class' => 'App\Test\Dummy',
                'lang' => [
                    'en',
                    'fr' => [
                        200,
                        '%class%',
                        '%%foo%%',
                    ],
                ],
                'refToClass' => '%class%',
                'hello' => 'world',
                'escapedVal1' => '%%hello%%',
                'escapedVal2' => '%hello%%',
                'escapedVal3' => '%%hello%',
                'refToNextParam' => '%nextParam%',
                'nextParam' => 'nextVal',
                'configVal' => '%locale.default%',
                'envVal' => '%env.test.value%',
            ],
            [
                'boolParam' => true,
                'intParam' => 2000,
                'floatParam' => -.89,
                'objectParam' => new \stdClass(),
                'closureParam' => function () {},
                'class' => 'App\Test\Dummy',
                'lang' => [
                    'en',
                    'fr' => [
                        200,
                        'App\Test\Dummy',
                        '%foo%',
                    ],
                ],
                'refToClass' => 'App\Test\Dummy',
                'hello' => 'world',
                'escapedVal1' => '%hello%',
                'escapedVal2' => 'world%',
                'escapedVal3' => '%hello%',
                'refToNextParam' => 'nextVal',
                'nextParam' => 'nextVal',
                'configVal' => 'en-GB',
                'envVal' => 'DummyEnvValue',
            ]
        ];
    }
}

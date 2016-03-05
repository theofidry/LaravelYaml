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

use Fidry\LaravelYaml\DependencyInjection\Resolver\BuildedParameterResolver;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryInterface;
use Prophecy\Argument;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Resolver\BuildedParameterResolver
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class BuildedParameterResolverTest extends \PHPUnit_Framework_TestCase
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
        $resolver = new BuildedParameterResolver($parameters, $config);

        foreach ($expected as $parameter => $expectedValue) {
            $actual = $resolver->resolve($parameter);
            $this->assertEquals(
                $expectedValue,
                $actual,
                sprintf(
                    '"%s" did not match "%s" for parameter "%s"',
                    var_export($actual, true),
                    var_export($expected[$parameter], true),
                    $parameter
                )
            );
        }
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\ParameterNotFoundException
     */
    public function testResolveParametersWithUnexistingParameter()
    {
        $resolver = new BuildedParameterResolver([], $this->config);
        $resolver->resolve('%hello.world%');
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
                'closureParam' => function () { },
                'class' => 'App\Test\Dummy',
                'lang' => [
                    'en',
                    'fr' => [
                        200,
                        '%class%',
                        '%%foo%%',
                    ],
                ],
                'refToClass' => 'App\Test\Dummy',
                'escapedVal1' => '%%dummy%%',
                'escapedVal2' => '%dummy%%',
                'escapedVal3' => '%%dummy%',
            ],
            [
                'boolParam' => true,
                'intParam' => 2000,
                'floatParam' => -.89,
                'objectParam' => new \stdClass(),
                'closureParam' => function () { },
                'class' => 'App\Test\Dummy',
                'lang' => [
                    'en',
                    'fr' => [
                        200,
                        'App\Test\Dummy',
                        '%%foo%%',
                    ],
                ],
                'refToClass' => 'App\Test\Dummy',
                'escapedVal1' => '%%dummy%%',
                'escapedVal2' => '%dummy%%',
                'escapedVal3' => '%%dummy%',

                '%locale.default%' => 'en-GB',
                '%env.test.value%' => 'dummy',
            ]
        ];
    }
}

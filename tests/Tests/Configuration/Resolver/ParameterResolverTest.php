<?php

namespace Fidry\LaravelYaml\Tests\Configuration\Resolver;

use Fidry\LaravelYaml\Configuration\Resolver\ParameterResolver;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryInterface;
use Prophecy\Argument;

/**
 * @covers             Configuration\Resolver\ParameterResolver
 *
 * @author             Théo FIDRY <theo.fidry@gmail.com>
 */
class ParameterResolverTest extends \PHPUnit_Framework_TestCase
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
//        $resolver = new ParameterResolver($parameters, $config);
//        $actual = $resolver->resolve();
//
//        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\Configuration\Resolver\ParameterCircularReferenceException
     */
    public function testResolveParametersWithLoop()
    {
        $resolver = new ParameterResolver(
            [
                'foo' => '%bar%',
                'bar' => '%foo%',
            ],
            $this->config
        );
        $resolver->resolve();
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\ParameterNotFoundException
     */
    public function testResolveParametersWithUnexistingParameter()
    {
        $resolver = new ParameterResolver(
            [
                'foo' => '%hello.world%',
            ],
            $this->config
        );
        $resolver->resolve();
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
                'escapedVal1' => '%%dummy%%',
                'escapedVal2' => '%dummy%%',
                'escapedVal3' => '%%dummy%',
                'refToNextParam' => '%nextParam%',
                'nextParam' => 'nextVal',
                'configVal' => '%locale.default%',
                'envVal' => '%env.test.value%',
            ],
            [
                'boolParam' => true,
                'intParam' => 2000,
                'floatParam' => -.89,
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
                'refToNextParam' => 'nextVal',
                'nextParam' => 'nextVal',
                'configVal' => 'en-GB',
                'envVal' => 'dummy',
            ]
        ];
    }
}

<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests;

use Fidry\LaravelYaml\Test\AnotherDummyService;
use Fidry\LaravelYaml\Test\DummyInterface;
use Fidry\LaravelYaml\Test\DummyService;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelInterface;
use Illuminate\Contracts\Foundation\Application;

/**
 * @coversNothing
 *
 * @author             Théo FIDRY <theo.fidry@gmail.com>
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private static $app;

    /**
     * @var ConsoleKernelInterface
     */
    private static $kernel;

    public function setUp()
    {
        /* @var Application $app */
        $app = require __DIR__.'/../Functional/bootstrap.php';
        $kernel = $app->make(ConsoleKernelInterface::class);
        if ($kernel instanceof ConsoleKernelInterface === false) {
            throw new \InvalidArgumentException();
        }
        /* @var ConsoleKernelInterface $kernel */
        $kernel->bootstrap();

        static::$app = $app;
        static::$kernel = $kernel;
    }

    public function testRegisterParameters()
    {
        $expected = [
            'other_config_val_before' => 'http://localhost',
            'env_val' => 'http://localhost',
            'true_param' => true,
            'int_param' => '2000',
            'application.class' => 'Fidry\LaravelYaml\Test\Foundation\ApplicationMock',
            'config_value' => 'en',
            'other_config_val' => 'Fidry\LaravelYaml\Test\Foundation\ApplicationMock',
            'fooo' => '%%bar%%',
            'fooobar' => '%%%bar2%%%%',
            'lang' => [
                'en',
                'fr',
                'http://localhost',
                true,
            ],
            'weirdname' => 'hello',
            'another-weird-name' => 'dummy',
            'spaced ?!& name' => 'dummy',
            'array_param' => [
                'something' => [
                    'somethingElseWithWeirdName' => true,
                    'foo' => [
                        'bar',
                    ],
                ],
            ],
            'service_param' => 'yo',
        ];

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, static::$app[$key], sprintf('Failed to equality for paremter "%s"', $key));
        }
    }

    public function testRegisterServices()
    {
        /* @var DummyService $dummy */
        $dummy = static::$app->make('dummy');
        $this->assertInstanceOf(DummyService::class, $dummy);

        $this->assertSame(static::$app->make('foo'), $dummy);

        /* @var AnotherDummyService $anotherDummy */
        $anotherDummy = static::$app->make('another_dummy');
        $this->assertInstanceOf(AnotherDummyService::class, $anotherDummy);
        $this->assertSame(static::$app->make(DummyInterface::class), $anotherDummy);

        $this->assertEquals('yo', $anotherDummy->getParams()[0]);
        $this->assertEquals(null, $anotherDummy->getParams()[1]);
        $this->assertSame($dummy, $anotherDummy->getParams()[2]);

        $this->assertSame(
            [
                $anotherDummy,
            ],
            static::$app->tagged('dummies')
        );
        $this->assertSame(
            [
                $anotherDummy,
            ],
            static::$app->tagged('random_tag')
        );
    }
}

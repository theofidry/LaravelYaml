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
use Fidry\LaravelYaml\Test\DummyFactory;
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

    public function testParametersAreRegistered()
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
            $this->assertEquals($value, static::$app[$key], sprintf('Failed to equality for parameter "%s"', $key));
        }
    }

    public function testParametersImportedAreRegistered()
    {
        $this->assertTrue(static::$app['imported_param']);
    }

    public function testDummyServiceIsRegistered()
    {
        $this->assertInstanceOf(DummyService::class, static::$app->make('dummy'));
    }

    public function testFooServiceIsRegistered()
    {
        $this->assertSame(static::$app->make('foo'), static::$app->make('dummy'));
    }

    public function testAnotherDummyServiceIsRegistered()
    {
        /* @var AnotherDummyService $service */
        $service = static::$app->make('another_dummy');
        $this->assertInstanceOf(AnotherDummyService::class, $service);
        $this->assertSame(static::$app->make(DummyInterface::class), $service);

        $this->assertEquals('yo', $service->getParams()[0]);
        $this->assertEquals(null, $service->getParams()[1]);
        $this->assertSame(static::$app->make('dummy'), $service->getParams()[2]);

        $this->assertSame(
            [
                $service,
            ],
            static::$app->tagged('dummies')
        );
        $this->assertSame(
            [
                $service,
            ],
            static::$app->tagged('random_tag')
        );
    }

    public function testAnotherFactoryDummy1ServiceIsRegistered()
    {
        /* @var AnotherDummyService $service */
        $service = static::$app->make('fdummy1');
        $this->assertInstanceOf(AnotherDummyService::class, $service);
        $this->assertNotSame(static::$app->make(DummyInterface::class), $service);
    }

    public function testAnotherFactoryDummy2ServiceIsRegistered()
    {
        /* @var AnotherDummyService $service */
        $service = static::$app->make('fdummy2');
        $this->assertInstanceOf(AnotherDummyService::class, $service);
        $this->assertNotSame(static::$app->make(DummyInterface::class), $service);

        $this->assertEquals('yo', $service->getParams()[0]);
    }

    public function testDummyFactoryServiceIsRegistered()
    {
        /* @var DummyFactory $service */
        $service = static::$app->make('dummy_factory');
        $this->assertInstanceOf(DummyFactory::class, $service);
        $this->assertSame(static::$app->make(DummyFactory::class), $service);
    }

    public function testAnotherFactoryDummy3ServiceIsRegistered()
    {
        /* @var AnotherDummyService $service */
        $service = static::$app->make('fdummy3');
        $this->assertInstanceOf(AnotherDummyService::class, $service);
        $this->assertNotSame(static::$app->make(DummyInterface::class), $service);

        $this->assertEquals('yo', $service->getParams()[0]);
    }

    public function testAnotherDecoratedDummyIsRegistered()
    {
        /* @var AnotherDummyService $ddanother_dummy */
        $ddanother_dummy = static::$app->make('ddummy');
        /* @var AnotherDummyService $danother_dummy */
        $danother_dummy = static::$app->make('surprise');
        /* @var DummyService $danother_dummy */
        $ddummy = static::$app->make('ddummy.inner');

        $this->assertInstanceOf(AnotherDummyService::class, $ddanother_dummy);
        $this->assertInstanceOf(AnotherDummyService::class, $danother_dummy);
        $this->assertInstanceOf(DummyService::class, $ddummy);

        $this->assertSame($danother_dummy, $ddanother_dummy->getParams()[0]);
        $this->assertSame($ddummy, $danother_dummy->getParams()[0]);
    }
}

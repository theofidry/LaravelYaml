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

use Fidry\LaravelYaml\Test\AnotherDummy;
use Fidry\LaravelYaml\Test\Dummy;
use Fidry\LaravelYaml\Test\SimpleDummy;
use Fidry\LaravelYaml\Test\UnwirableDummy;
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
            'null_param' => null,
            'other_config_val_before' => 'http://localhost',
            'env_val' => 'http://localhost',
            'true_param' => true,
            'int_param' => '2000',
            'application.class' => 'Fidry\LaravelYaml\Test\Foundation\ApplicationMock',
            'config_value' => 'en',
            'other_config_val' => 'Fidry\LaravelYaml\Test\Foundation\ApplicationMock',
            'hello' => 'world',
            'escaped_percent_sign' => '%hello%',
            'escaped_percent_sign_with_parameter' => '%world%%',
            'double_escaped_percent_sign' => '%%hello%%',
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
            'service_param' => 'foobar',
            'expression_param' => \DateTime::ATOM,
            'composite1' => 'hello world!',
            'composite2' => 2000,
            'composite_parameter' => 'hey! hello world!.2000',
        ];

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, static::$app[$key], sprintf('Failed to equality for parameter "%s"', $key));
        }
    }

    public function testParametersImportedAreRegistered()
    {
        $this->assertTrue(static::$app['imported_param']);
    }

    public function testSimpliestServiceIsRegistered()
    {
        $this->assertInstanceOf(SimpleDummy::class, static::$app->make('simple_dummy'));
    }

    public function testUnwirableServiceCannotBeAutowired()
    {
        try {
            static::$app->make('unwirable_dummy');
            $this->fail('Expected exception to be thrown');
        } catch (\Exception $e) {
        } catch (\Throwable $e) {
        }
    }

    public function testUnwirableServiceIsRegistered()
    {
        $this->assertInstanceOf(UnwirableDummy::class, static::$app->make('resolved_unwirable_dummy'));
    }

    public function testAliasesAreRegistered()
    {
        $this->assertInstanceOf(Dummy::class, static::$app->make('dummy'));

        $this->assertInstanceOf(
            UnwirableDummy::class,
            static::$app->make('resolved_unwirable_dummy_with_alias')
        );
        $this->assertSame(
            static::$app->make('resolved_unwirable_dummy_with_alias'),
            static::$app->make('rudwa')
        );
    }

    public function testServiceWithArgumentsIsRegistered()
    {
        /* @var UnwirableDummy $service */
        $service = static::$app->make('service_with_arguments');

        $dummy = static::$app->make('dummy');
        $this->assertSame($dummy, $service->getInterfaceArg());

        $this->assertCount(3, $service->getArgs());
        $this->assertEquals('foobar', $service->getArgs()[0]);
        $this->assertEquals('foo', $service->getArgs()[1]);
        $this->assertSame('Fidry\LaravelYaml\Test\Dummy', $service->getArgs()[2]);
    }

    public function testServiceWithOptionalServiceAsArgumentIsRegistered()
    {
        /* @var UnwirableDummy $service */
        $service = static::$app->make('service_with_arguments_and_optional_service');
        $this->assertCount(1, $service->getArgs());
        $this->assertEquals(null, $service->getArgs()[0]);
    }

    /**
     * @expectedException \Fidry\LaravelYaml\Exception\ServiceNotFoundException
     */
    public function testServiceWithIndexistingServiceAsArgumentsIsRegistered()
    {
        /* @var UnwirableDummy $service */
        $service = static::$app->make('service_with_arguments_and_inexisting_service');
        $this->assertCount(1, $service->getArgs());
        $this->assertEquals(null, $service->getArgs()[0]);
    }

    public function testServiceWithAutowiringTypesIsRegistered()
    {
        /* @var UnwirableDummy $service */
        $service = static::$app->make('service_with_autowiring_types');
        $this->assertSame(
            static::$app->make('Fidry\LaravelYaml\Test\AutowiringInterface'),
            $service
        );
    }

    public function testServiceWithWeirdAutowiringTypesIsRegistered()
    {
        /* @var UnwirableDummy $service */
        $service = static::$app->make('service_with_weird_autowiring_types');
        $this->assertSame(
            static::$app->make('Fidry\LaravelYaml\Test\UnknownInterface'),
            $service
        );
        $this->assertSame(
            static::$app->make('notAClass'),
            $service
        );
    }

    public function testTaggedServicesAreRegistered()
    {
        $taggedService1 = static::$app->make('tagged_dummy1');
        $taggedService2 = static::$app->make('tagged_dummy2');
        $this->assertSame(
            [
                $taggedService1,
            ],
            static::$app->tagged('random_tag')
        );
        $this->assertSame(
            [
                $taggedService1,
                $taggedService2,
            ],
            static::$app->tagged('dummies')
        );
    }

    public function testServicesCreatedByStaticFactoryAreRegistered()
    {
        $this->assertInstanceOf(AnotherDummy::class, static::$app->make('dummy_from_static_factory'));
        
        /* @var AnotherDummy $service */
        $service = static::$app->make('dummy_from_static_factory_with_args');
        $this->assertInstanceOf(AnotherDummy::class, $service);
        $this->assertCount(1, $service->getArgs());
        $this->assertEquals('foobar', $service->getArgs()[0]);
    }

    public function testServicesCreatedByFactoryAreRegistered()
    {
        $this->assertInstanceOf(AnotherDummy::class, static::$app->make('dummy_from_factory'));

        /* @var AnotherDummy $service */
        $service = static::$app->make('dummy_from_factory_with_args');
        $this->assertInstanceOf(AnotherDummy::class, $service);
        $this->assertCount(1, $service->getArgs());
        $this->assertEquals('foobar', $service->getArgs()[0]);
    }

    public function testAnotherDecoratedDummyIsRegistered()
    {
        $dummyForFoo = static::$app->make('dummy_for_foo');
        $dummyForBar = static::$app->make('dummy_for_bar');
        $dummyForBooze = static::$app->make('dummy_for_booze');

        /* @var UnwirableDummy $foo */
        $foo = static::$app->make('foo');
        /* @var UnwirableDummy $surprise */
        $surprise = static::$app->make('surprise');
        /* @var UnwirableDummy $barInner */
        $barInner = static::$app->make('bar.inner');

        $this->assertInstanceOf(UnwirableDummy::class, $foo);
        $this->assertInstanceOf(UnwirableDummy::class, $surprise);
        $this->assertInstanceOf(UnwirableDummy::class, $barInner);

        $this->assertSame($dummyForBooze, $foo->getInterfaceArg());
        $this->assertSame($surprise, $foo->getArgs()[0]);

        $this->assertSame($dummyForFoo, $barInner->getInterfaceArg());

        $this->assertSame($dummyForBar, $surprise->getInterfaceArg());
        $this->assertSame($barInner, $surprise->getArgs()[0]);
    }

    public function testServiceRegisteredInProvidersCanUseTheLibraryServices()
    {
        $dummies = static::$app->tagged('dummies');
        $this->assertSame($dummies, static::$app->make('dummies_chain')->getDummies());
    }
}

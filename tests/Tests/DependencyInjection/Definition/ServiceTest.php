<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Tests\DependencyInjection\Definition;

use Fidry\LaravelYaml\DependencyInjection\Definition\Service;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Definition\Service
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $service = new Service('serviceId', 'App\Dummy');

        $this->assertEquals('serviceId', $service->getName());
        $this->assertEquals('App\Dummy', $service->getClass());
        $this->assertEquals([], $service->getArguments());
        $this->assertEquals([], $service->getAutowiringTypes());
        $this->assertEquals([], $service->getTags());
    }

    public function testFullConstruct()
    {
        $service = new Service(
            'serviceId',
            'App\Dummy',
            [
                'arg'
            ],
            [
                'autowiringType'
            ],
            [
                'tagName' => [
                    'attributeName' => 'attributeValue',
                ]
            ]
        );

        $this->assertEquals('serviceId', $service->getName());
        $this->assertEquals('App\Dummy', $service->getClass());
        $this->assertEquals(
            [
                'arg'
            ],
            $service->getArguments()
        );
        $this->assertEquals(
            [
                'autowiringType'
            ],
            $service->getAutowiringTypes()
        );
        $this->assertEquals(
            [
                'tagName' => [
                    'attributeName' => 'attributeValue',
                ]
            ],
            $service->getTags()
        );
    }
}

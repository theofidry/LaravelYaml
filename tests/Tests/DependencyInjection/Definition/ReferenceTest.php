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

use Fidry\LaravelYaml\DependencyInjection\Builder\BuilderInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Reference;

/**
 * @covers Fidry\LaravelYaml\DependencyInjection\Definition\Reference
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testReferenceIgnoredOnInvalidBehaviour()
    {
        $reference = new Reference('dummy', BuilderInterface::IGNORE_ON_INVALID_REFERENCE);

        $this->assertEquals('dummy', $reference->getId());
        $this->assertTrue($reference->ignoreOnInvalidBehaviour());
        $this->assertFalse($reference->throwExceptionOnInvalidBehaviour());
        $this->assertFalse($reference->returnNullOnInvalidBehaviour());
    }

    public function testReferenceThrowingExceptionOnInvalidBehaviour()
    {
        $reference = new Reference('dummy', BuilderInterface::EXCEPTION_ON_INVALID_REFERENCE);

        $this->assertEquals('dummy', $reference->getId());
        $this->assertFalse($reference->ignoreOnInvalidBehaviour());
        $this->assertTrue($reference->throwExceptionOnInvalidBehaviour());
        $this->assertFalse($reference->returnNullOnInvalidBehaviour());
    }

    public function testReferenceReturnNullOnInvalidBehaviour()
    {
        $reference = new Reference('dummy', BuilderInterface::NULL_ON_INVALID_REFERENCE);

        $this->assertEquals('dummy', $reference->getId());
        $this->assertFalse($reference->ignoreOnInvalidBehaviour());
        $this->assertFalse($reference->throwExceptionOnInvalidBehaviour());
        $this->assertTrue($reference->returnNullOnInvalidBehaviour());
    }

    /**
     * @dataProvider provideUnknownInvalidBehaviours
     */
    public function testReferenceWithUnknownInvalidBehaviour($unknowBehaviour)
    {
        $reference = new Reference('dummy', $unknowBehaviour);

        $this->assertEquals('dummy', $reference->getId());
        $this->assertFalse($reference->ignoreOnInvalidBehaviour());
        $this->assertFalse($reference->throwExceptionOnInvalidBehaviour());
        $this->assertFalse($reference->returnNullOnInvalidBehaviour());
    }

    public function provideUnknownInvalidBehaviours()
    {
        return [
            [-1],
            [null],
            [100],
        ];
    }
}

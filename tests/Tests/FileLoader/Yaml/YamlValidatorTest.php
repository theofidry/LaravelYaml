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

use Fidry\LaravelYaml\FileLoader\Yaml\YamlValidator;

/**
 * @covers Fidry\LaravelYaml\FileLoader\Yaml\YamlValidator
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class YamlValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YamlValidator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new YamlValidator();
    }

    public function testValidateNullContent()
    {
        $actual = $this->validator->validate(null, 'dummy.yml');
        $this->assertNull($actual);
    }

    /**
     * @dataProvider provideValidNamespaces
     */
    public function testValidateValidNamespacesContent($namespaces)
    {
        $actual = $this->validator->validate($namespaces, 'dummy.yml');
        $this->assertEquals($namespaces, $actual);
    }

    /**
     * @dataProvider provideInvalidNamespaces
     *
     * @expectedException \Fidry\LaravelYaml\Exception\Configuration\InvalidArgumentException
     */
    public function testValidateInvalidNamespacesContent($namespaces)
    {
        $this->validator->validate($namespaces, 'dummy.yml');
    }

    public function provideValidNamespaces()
    {
        yield [
            [
                'parameters' => null,
            ],
        ];
        yield [
            [
                'services' => null,
            ],
        ];
        yield [
            [
                'parameters' => null,
                'services' => null,
            ],
        ];
    }

    public function provideInvalidNamespaces()
    {
        yield [
            'is not array',
        ];
        yield [
            [
                'unknown' => null,
            ],
        ];
        yield [
            [
                'parameters' => null,
                'unknown' => null,
            ],
        ];
        yield [
            [
                'services' => null,
                'unknown' => null,
            ],
        ];
        yield [
            [
                'parameters' => null,
                'services' => null,
                'unknown' => null,
            ],
        ];
    }
}

<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\FileLoader\Parser\Yaml\Util;

use Fidry\LaravelYaml\DependencyInjection\Definition\FactoryInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\Factory;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class FactoryParser
{
    /**
     * @var ResolverInterface
     */
    private $serviceResolver;

    public function __construct(ResolverInterface $serviceResolver)
    {
        $this->serviceResolver = $serviceResolver;
    }

    /**
     * Parses a factory service definition and return the factory object.
     *
     * @param ServiceInterface $service
     * @param mixed            $factory
     * @param string           $fileName file name
     *
     * @return ServiceInterface
     * @throws InvalidArgumentException
     */
    public function parse(ServiceInterface $service, $factory, $fileName)
    {
        $this->checkFactory($service, $factory, $fileName);

        return $this->parseFactory($service, $factory[0], $factory[1], $fileName);
    }

    /**
     * @param ServiceInterface $service
     * @param mixed            $factory
     * @param string           $fileName file name
     *
     * @throws InvalidArgumentException
     */
    private function checkFactory(ServiceInterface $service, $factory, $fileName)
    {
        if (false === is_array($factory)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "factory" must be an array for service "%s", but found "%s" instead in %s. Check your YAML syntax.',
                    $service->getName(),
                    gettype($factory),
                    $fileName
                )
            );
        }

        if (2 !== count($factory)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "factory" expects two parameters for service "%s", but found "%s" instead in %s. Check your YAML syntax.',
                    $service->getName(),
                    gettype($factory),
                    $fileName
                )
            );
        }
    }

    /**
     * @param ServiceInterface $service
     * @param mixed            $factoryClass
     * @param mixed            $factoryMethod
     * @param string           $fileName file name
     *
     * @return FactoryInterface
     * @throws InvalidArgumentException
     */
    private function parseFactory(ServiceInterface $service, $factoryClass, $factoryMethod, $fileName)
    {
        if (false === is_string($factoryClass)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The first parameter of "factory" for service "%s" must be a class name or a reference to a service, but found "%s" instead in %s. Check your YAML syntax.',
                    $service->getName(),
                    gettype($factoryClass),
                    $fileName
                )
            );
        }
        $factoryClass = $this->serviceResolver->resolve($factoryClass);

        if (false === is_string($factoryMethod)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The second parameter of "factory" for service "%s" must be a class name or a reference to a service, but found "%s" instead in %s. Check your YAML syntax.',
                    $service->getName(),
                    gettype($factoryMethod),
                    $fileName
                )
            );
        }

        return new Factory($service, $factoryClass, $factoryMethod);
    }
}

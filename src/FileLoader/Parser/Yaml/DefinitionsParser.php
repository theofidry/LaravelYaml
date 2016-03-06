<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\FileLoader\Parser\Yaml;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Definition\Alias;
use Fidry\LaravelYaml\DependencyInjection\Definition\FactoryInterface;
use Fidry\LaravelYaml\DependencyInjection\Definition\FactoryService;
use Fidry\LaravelYaml\DependencyInjection\Definition\Service;
use Fidry\LaravelYaml\DependencyInjection\Definition\ServiceInterface;
use Fidry\LaravelYaml\Exception\FileLoader\InvalidArgumentException;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ResolverInterface;
use Fidry\LaravelYaml\FileLoader\Parser\Resolver\ServiceResolver;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class DefinitionsParser
{
    /**
     * @var ResolverInterface
     */
    private $serviceResolver;

    public function __construct(ResolverInterface $serviceResolver = null)
    {
        $this->serviceResolver = (null === $serviceResolver)? new ServiceResolver(): $serviceResolver;
    }

    /**
     * Parses service definitions and register them to the container.
     *
     * @param ContainerBuilder $container
     * @param array            $content  YAML file content
     * @param string           $fileName file name
     *
     * @throws InvalidArgumentException
     */
    public function parse(ContainerBuilder $container, $content, $fileName)
    {
        if (!isset($content['services'])) {
            return;
        }

        if (!is_array($content['services'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'The "services" key should contain an array in %s. Check your YAML syntax.',
                    $fileName
                )
            );
        }

        foreach ($content['services'] as $id => $service) {
            $this->parseDefinition($container, strtolower($id), $service, $fileName);
        }
    }

    /**
     * Parses a service definition and register it to the container.
     *
     * @param ContainerBuilder $container
     * @param string           $id
     * @param array|string     $service
     * @param string           $fileName file name
     *
     * @throws InvalidArgumentException
     */
    private function parseDefinition(ContainerBuilder $container, $id, $service, $fileName)
    {
        if (is_string($service) && 0 === strpos($service, '@')) {
            $alias = new Alias($id, substr($service, 1));
            $container->addAlias($alias);

            return;
        }

        if (false === is_array($service)) {
            throw new InvalidArgumentException(
                sprintf(
                    'A service definition must be an array or a string starting with "@" but %s found for service "%s" in %s. Check your YAML syntax.',
                    gettype($service),
                    $id,
                    $fileName
                )
            );
        }

        if (isset($service['alias'])) {
            $aliasName = $service['alias'];
            if (false === is_string($aliasName)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Parameter "alias" must be a plain name for service "%s", but found "%s" instead in %s. Check your YAML syntax.',
                        $id,
                        gettype($aliasName),
                        $fileName
                    )
                );
            }

            $alias = new Alias($aliasName, $id);
            $container->addAlias($alias);
        }

        $class = $this->getClass($id, $service, $fileName);
        $arguments = (isset($service['arguments']))
            ? $this->serviceResolver->resolve($service['arguments'])
            : []
        ;
        $tags = $this->getTags($id, $service, $fileName);
        $autowiringTypes = $this->getAutowiringTypes($id, $service, $fileName);

        $serviceDefinition = new Service($id, $class, $arguments, $autowiringTypes, $tags);

        if (isset($service['factory'])) {
            $serviceDefinition = $this->parseFactory($serviceDefinition, $service['factory'], $fileName);
        }

        $container->addService($serviceDefinition);
    }

    /**
     * @param string $id
     * @param array  $service
     * @param string $fileName
     *
     * @return string
     * @throws InvalidArgumentException
     */
    private function getClass($id, $service, $fileName)
    {
        if (false === isset($service['class'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "class" missing for service "%s" in %s. Check your YAML syntax.',
                    $id,
                    $fileName
                )
            );
        }
        if (false === is_string($service['class'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "class" must be a FQCN for service "%s", but found "%s" instead in %s. Check your YAML syntax.',
                    $id,
                    gettype($service['class']),
                    $fileName
                )
            );
        }

        return $service['class'];
    }

    /**
     * @param string $id
     * @param array  $service
     * @param string $fileName
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function getTags($id, $service, $fileName)
    {
        if (false === isset($service['tags'])) {
            return [];
        }

        $tags = [];
        if (false === is_array($service['tags'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "tags" must be an array for service "%s" in %s. Check your YAML syntax.',
                    $id,
                    $fileName
                )
            );
        }

        foreach ($service['tags'] as $tag) {
            if (false === is_array($tag)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'A "tags" entry must be an array for service "%s" in %s. Check your YAML syntax.',
                        $id,
                        $fileName
                    )
                );
            }

            if (false === isset($tag['name'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'A "tags" entry is missing a "name" key for service "%s" in %s.',
                        $id,
                        $fileName
                    )
                );
            }

            $name = strtolower($tag['name']);
            unset($tag['name']);

            foreach ($tag as $attribute => $value) {
                if (false === is_scalar($value) && null !== $value) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'A "tags" attribute must be of a scalar-type for service "%s", tag "%s", attribute "%s" in %s. Check your YAML syntax.',
                            $id,
                            $name,
                            $attribute,
                            $fileName
                        )
                    );
                }
            }

            $tags[$name] = $tag;
        }

        return $tags;
    }

    /**
     * @param string $id
     * @param array  $service
     * @param string $fileName
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function getAutowiringTypes($id, $service, $fileName)
    {
        if (false === isset($service['autowiringTypes'])) {
            return [];
        }

        if (false === is_array($service['autowiringTypes'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "autowiringTypes" must be an array for service "%s" in %s. Check your YAML syntax.',
                    $id,
                    $fileName
                )
            );
        }

        $autowiringTypes = [];
        foreach ($service['autowiringTypes'] as $autowiringType) {
            if (false === is_string($autowiringType)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'A "autowiringType" entry must be a FQCN for service "%s" in %s. Check your YAML syntax.',
                        $id,
                        $fileName
                    )
                );
            }

            $autowiringTypes[$autowiringType] = true;
        }

        return array_keys($autowiringTypes);
    }

    /**
     * Parses a factory service definition and return the factory object.
     *
     * @param ServiceInterface $service
     * @param mixed            $factory
     * @param string           $fileName file name
     *
     * @return FactoryInterface
     * @throws InvalidArgumentException
     */
    private function parseFactory( ServiceInterface $service, $factory, $fileName)
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

        $factoryClass = $factory[0];
        $factoryMethod = $factory[1];
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

       return new FactoryService($service, $factoryClass, $factoryMethod);
    }
}

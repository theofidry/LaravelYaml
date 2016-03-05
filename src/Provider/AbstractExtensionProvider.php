<?php

namespace Fidry\LaravelYaml\Provider;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Extension\ExtensionInterface;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
abstract class AbstractExtensionProvider extends IlluminateServiceProvider implements ProviderInterface
{
    /**
     * @var ExtensionInterface[]
     */
    private $extensions = [];

    /**
     * {@inheritdoc}
     */
    final public function register()
    {
        $this->loadExtensions();

        $container = new ContainerBuilder();
        foreach ($this->extensions as $extension) {
            $extension->load($container);
        }

        $container->build($this->app);
    }

    /**
     * Loads all the extensions registered by the user.
     */
    private function loadExtensions()
    {
        $extensionsClassNames = array_flip($this->getExtensions());

        foreach ($extensionsClassNames as $extensionClassName => $null) {
            if (false === class_exists($extensionClassName)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Unable to load extension "%s": extension not found',
                        $extensionClassName
                    )
                );
            }

            $extension = new $extensionClassName();
            if (false === $extension instanceof ExtensionInterface) {
                throw new \LogicException(
                    sprintf(
                        'Extension "%s" must implement the interface "%s"',
                        get_class($extension),
                        ExtensionInterface::class
                    )
                );
            }

            $this->extensions[] = $extension;
        }
    }
}

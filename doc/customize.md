# Customize

1. [Import other files](#import-other-files)
1. [Use your own provider](#use-your-own-provider)

Out of the box, you must respect the following directory structure:

```
resources/
    providers/
        parameters.yml
        parameters_<environment>.yml
        services.yml
```

However you might want to include other files as well, or simply place same
in another folder. To do that you have two ways.

### Import other files

You can import other YAML files via the `imports` directive. For example, if you
have:

```yaml
# resouces/providers/parameters.yml

imports:
    - { resource: 'imported_parameters.yml' }
```

Then the file `resouces/providers/imported_parameters.yml` will be loaded with
`resouces/providers/parameters.yml` will be loaded.

### Use your own provider

For an easy install, this library comes with
[`Fidry\LaravelYaml\Provider\DefaultExtensionProvider`](src/Provider/DefaultExtensionProvider.php)
providing the default directory structure. However you are free to create your
own provider to load any files you want. For that, create your `Extension`,
responsible for loading your files:

```php
namespace App\DependencyInjection;

use Fidry\LaravelYaml\DependencyInjection\Builder\ContainerBuilder;
use Fidry\LaravelYaml\DependencyInjection\Extension\ExtensionInterface;
use Fidry\LaravelYaml\FileLoader\Yaml\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

final class AppExtension implements ExtensionInterface
{
    public function load(ContainerBuilder $container)
    {
        $rootDir = new FileLocator(resource_path('my_folder'));
        $loader = new YamlFileLoader($container, $rootDir);

        $this
            ->load('my_parameters.yml')
            ->load('myservices.yml')
        ;
    }
}
```

Then have a provider extending
[`AbstractExtensionProvider`](src/Provider/AbstractExtensionProvider.php)
(recommended) or implementing the [`ProviderInterface`](src/Provider/ProviderInterface.php)
to register your extension:

```php
namespace App\Providers;

use Fidry\LaravelYaml\Provider\AbstractExtensionProvider;

class AppServiceProvider extends AbstractExtensionProvider
{
    //...

    public function getExtensions()
    {
        return [
            \App\DependencyInjection\AppExtension::class,
        ];
    }
}
```

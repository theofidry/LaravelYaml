# Laravel Yaml ServiceProvider

[![Package version](http://img.shields.io/packagist/v/theofidry/laravel-yaml.svg?style=flat-square)](https://packagist.org/packages/theofidry/laravel-yaml)
[![Build Status](https://img.shields.io/travis/theofidry/LaravelYaml.svg?branch=master&style=flat-square)](https://travis-ci.org/theofidry/LaravelYaml?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/71daba3d-2706-4387-9d47-434db443d310.svg?style=flat-square)](https://insight.sensiolabs.com/projects/71daba3d-2706-4387-9d47-434db443d310)
[![Dependency Status](https://www.versioneye.com/user/projects/56ddab9d4839f70035207c01/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56ddab9d4839f70035207c01)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/theofidry/LaravelYaml.svg?style=flat-square)](https://scrutinizer-ci.com/g/theofidry/LaravelYaml/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/theofidry/LaravelYaml.svg?b=master&style=flat-square)](https://scrutinizer-ci.com/g/theofidry/LaravelYaml/?branch=master)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)

A simple Laravel library to declare your parmeters and services in [Yaml][1]
like in [Symfony][2]:

```yaml
# resources/providers/services.yml

services:
    dummy_service:
        class: App\Services\DummyService
        alias: dummy
        arguments:
            - %app.url%
            - %app.env%
```

instead of:

```php
# app/Providers/AppServiceProvider.php

//...
public function register()
{
    $this->app->singleton(
        'dummy_service',
        function ($app) {
            $url = env('APP_URL');
            $env = env('APP_ENV');

            return new \App\Services\DummyService($url, $env);
        }
    );
}
```

## Documentation

1. [Disclaimer: why using this library?](doc/disclaimer.md)
1. [Install](#install)
1. [Everything about parameters](doc/parameters.md)
  1. [YAML vs PHP](doc/parameters.md#yaml-vs-php)
  1. [Refering to another value](doc/parameters.md#refering-to-another-value)
  1. [Refering to an environment value](doc/parameters.md#refering-to-an-environment-value)
  1. [Overriding values](doc/parameters.md#overriding-values)
  1. [Environment dependent parameters](doc/parameters.md#environment-dependent-parameters)
1. [Service declaration](doc/services.md)
  1. [Simple services](doc/services.md#simple-services)
  1. [Factories](doc/services.md#factories)
  1. [Decorating services](doc/services.md#decorating-services)
1. [Custom file organisation](doc/customize.md)
  1. [Import other files](doc/customize.md#import-other-files)
  1. [Use your own provider](doc/customize.md#use-your-own-provider)

## Install

You can use [Composer](https://getcomposer.org/) to install the bundle to your project:

```bash
composer require theofidry/laravel-yaml
```

Then, add the provider [`Fidry\LaravelYaml\Provider\DefaultExtensionProvider`](src/Provider/DefaultExtensionProvider.php) to your application providers:

```php
<?php
// config/app.php

'providers' => [
    // ...
    \Fidry\LaravelYaml\Provider\DefaultExtensionProvider::class,
];
```

## Usage

See how to declare and use [parameters](doc/parameters.md) and
[services](doc/services.md)!

By convention, you should have the following structure:

```
resources/
    providers/
        parameters.yml
        parameters_testing.yml
        services.yml
```

The `parameters.yml` should contain all of your application parameters values:

```yaml
# resources/providers/parameters.yml

parameters:
    my_parameter: parameter_value
```

Depending of your environment, a second parameters file will be loaded. For
example, if your application environment (by default defined by the environment
variable `APP_ENV` in your `.env` file) is `'testing'` or `'production'`, the
library will try to load the `parameters_testing.yml` or `parameters_production.yml`
file.

Then `services.yml` should contain all your service definitions.

[See more.](#documentation)

## Credits

This bundle is developed by [Théo FIDRY](https://github.com/theofidry).

[1]: http://symfony.com/doc/current/components/yaml/yaml_format.html
[2]: http://symfony.com/doc/current/components/dependency_injection/advanced.html

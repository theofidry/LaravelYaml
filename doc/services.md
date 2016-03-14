# Service declaration

1. [Simple services](#simple-services)
1. [Factories](#factories)
1. [Decorating services](#decorating-services)

### Simple services

A complete service declartion is the following:

```yaml
# resources/providers/services.yml

services:
    dummy:
        class: App\Dummy
         # optional parameters
        alias: dudu # optional
        arguments:
            - %app.locale%
            - @mailer
            - @?log
        autowiringTypes: [App\DummyInterface]
        tags:
            - { name: dummies }
```

And is, assuming `app.locale` is a Laravel configuration value, the strict the
equivalent of:

```php
# app/Providers/AppServiceProvider.php

public function register()
{
    // ...
    $this->app->singleton(
        'dummy',
        function ($app) {
            $locale = config('app.locale');
            $mailer = $app['mailer'];
            try {
                $logger = $app['log'];
            } catch(\Exeception $e) {
                $logger = null;
            }

            return new \App\Dummy($locale, $mailer, $logger);
        }
    );
    $this->app->alias('dummy', 'dudu');
    $this->app->bind(\App\Dummy::class, 'dummy');
    $this->app->bind(\App\DummyInterface::class, 'dummy');
    $this->app->tag('dummy', ['dummies']);
}
```

Which is as you can see much more verbose. There is also a number of subtleties:

- `%app.locale%` refers to a parameter value, but it can be a parameter declared
  under the `parameters` namespace, a Laravel configuration value or an
  environment value.
- `@myService` is a reference to another service, here to the service of ID
  `myService`.
- `@?myService` is also a reference to the service `myService`, but specifies
  that this reference is **optional**. As a result if `myService` is not defined,
  then the returned value will be `null`, whereas `@myService` would throw an
  error in such cases.

### Factories

Suppose you have a factory `NewsletterManagerFactory` that returns a
`NewsletterManager` object:

```php
class NewsletterManagerFactory
{
    public static function createNewsletterManager()
    {
        $newsletterManager = new NewsletterManager();

        // ...

        return $newsletterManager;
    }
}
```

To make the `NewsletterManager` object available as a service, you can configure
declare it like so:

```yaml
services:
    newsletter_manager:
        class:   NewsletterManager
        factory: [NewsletterManagerFactory, createNewsletterManager]
```

Now, the method will be called statically. If the factory class itself should be
instantiated and the resulting object's method called, configure the factory
itself as a service. In this case, the method (e.g. `createNewsletterManager`)
should be changed to be non-static.

```yaml
services:
    newsletter_manager.factory:
        class: NewsletterManagerFactory

    newsletter_manager:
        class:   NewsletterManager
        factory: ['@newsletter_manager.factory', createNewsletterManager]
```

Which would be the PHP equivalent of:


```
# app/Providers/AppServiceProvider.php

public function register()
{
    // ...
    $this->app->singleton(
        'newsletter_manager',
        function ($app) {
            $factory = $app['newsletter_manager.factory'];

            return $factory->createNewsletterManager();
        }
    );
    $this->app->bind(NewsletterManager::class, 'dummy');
}
```

If you need to pass arguments to the factory method, you can use the `arguments`
options inside the service container. For example, suppose the
`createNewsletterManager method in the previous example takes the logging service
as an argument:

```yaml
services:
    newsletter_manager.factory:
        class: NewsletterManagerFactory

    newsletter_manager:
        class:   NewsletterManager
        factory: ['@newsletter_manager.factory', createNewsletterManager]
        arguments:
            - '@templating'
```

### Decorating Services

When overriding an existing definition, the old service is lost:

```yaml
services:
    foo:
        class: App\Foo

    foo:
        class: App\NewFoo
```

In Tinker, `app('newsletter_manager')` will give an instance of
`NewFoo` instead of `Foo`.

Most of the time, that's exactly what you want to do. But sometimes, you might
want to decorate the old one instead. In this case, the old service should be
kept around to be able to reference it in the new one. This configuration
replaces `foo` with a new one, but keeps a reference of the old one as
`bar.inner`:

```yaml
bar:
    class: stdClass
    decorates: foo
    arguments:
        - @bar.inner
```

Here is what's going on here: the application understand that the `bar` service
should replace the `foo` service, renaming `foo` to `bar.inner`. By convention,
the old `foo` service is going to be renamed `bar.inner`, so you can inject it
into your new service.

The generated inner id is based on the id of the decorator service (`bar` here),
not of the decorated service (`foo` here). This is mandatory to allow several
decorators on the same service (they need to have different generated inner ids).

You can change the inner service name if you want to:

```yaml
bar:
    class: stdClass
    decorates: foo
    decoration_inner_name: bar.wooz
    arguments:
        - @bar.wooz
```

There is no real equivalent of that with Laravel providers and this is made possible by the
way this library works.

# Parameters

1. [YAML vs PHP](doc/parameters.md#yaml-vs-php)
1. [Refering to another value](#refering-to-another-value)
1. [Refering to an environment value](#refering-to-environment-value)
1. [Overriding values](#overriding-values)
1. [Environment dependent parameters](#environment-dependent-parameters)

### YAML vs PHP

Parameters are simply configuration values. Instead of being declared in
`config/*.php`, they can be declared in your YAML files:

```
parameters:
    fetch: 8,
    default: mysql
    connections:
        mysql:
            driver: mysql
            host: %db.host%
            port: %db.port%
            database: %db.database%
            username: %db.username%
            password: %db.password%
            charset: utf8
            collation: utf8_unicode_ci
            prefix: ''
            strict: false
            engine: ~ # will be null
        pgsql:
            driver: pgsql
            host: %db.host%
            port: %db.port%
            database: %db.database%
            username: %db.username%
            password: %db.password%
            charset: utf8
            prefix: ''
            schema: public
    migrations: migrations
    redis:
        cluster: false
        default:
            host: %redis.host%
            password: %redis.password%
            port: %redis.port%
            database: 0
```

is the exact equivalent of:

```php
return [
    'fetch' => PDO::FETCH_CLASS,
    'default' => env('DB_CONNECTION'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
    ],
    'migrations' => 'migrations',
    'redis' => [
        'cluster' => false,
        'default' => [
            'host' => env('REDIS_HOST'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT'),
            'database' => 0,
        ],
    ],
];
```

### Refering to another value

Using the `%...%` syntax refers to another parameter, configuration value or
environment value. For example, if you have:

```yaml
parameters:
    foo: true
    bar: %foo%
```

The parameter `bar` will takes `foo` value, hence `true`. Typing `app('bar')`
in Tinker will give you `true`.

### Refering to a Laravel configuration value

On the same token, you can refer to a class Laravel configuration value:

```yaml
parameters:
    foo: %default.locale%
```

Provided `config('default.locale')` gives you `'en'`, then `app('foo')` will give
you `'en'` as well.

### Refering to an environment value

It also works with environment variables: if you have defined the environment
variable `APP_URL=http://localhost` and `APP_ENV=production`, then `%app.url%`
and `%app.env%` will give you `'http://localhost'` and `'production'`.

### Overriding values

The order in which values are being retrieved are:

1. Parameter value
2. Laravel configuration value
3. Environment value

As a result, if you have the environment variable `APP_URL=http://localhost`
with:

```yaml
parameters:
    app.url: http://example.com
    bar: %app.url%
```

Then the value of `bar` will be `'http://example.com'` instead of
`'http://localhost'`.

### Environment dependent parameters

You can also overide previously defined parameters values. For example if you
have:

```yaml
# resources/providers/parameters.yml

parameters:
    timer.class: App\Services\MyTimer
```

Your `timer.class` refers to the class name of `MyTimer`. But for some reason,
you want to use a mock instead in testing. Provided you application environment
(the value of `APP_ENV`) is `'testing'` (default Laravel value) during your
tests, you simply have to declare:

```yaml
# resources/providers/parameters_testing.yml

parameters:
    timer.class: App\Services\MyTimerMock
```

Et voilà! `timer.class` will have the value `MyTimerMock` instead of `MyTimer`
in your testing environment.

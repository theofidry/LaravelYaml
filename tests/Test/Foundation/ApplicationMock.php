<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\Test\Foundation;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ApplicationMock extends Container implements Application, \ArrayAccess
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        return $this->application = $application;
    }

    public function version()
    {
        return $this->application->version();
    }

    public function basePath()
    {
        return $this->application->basePath();
    }

    public function environment()
    {
        return $this->application->environment();
    }

    public function isDownForMaintenance()
    {
        return $this->application->isDownForMaintenance();
    }

    public function registerConfiguredProviders()
    {
        $this->application->registerConfiguredProviders();
    }

    public function register($provider, $options = [], $force = false)
    {
        return $this->application->register($provider, $options, $force);
    }

    public function registerDeferredProvider($provider, $service = null)
    {
        $this->application->registerDeferredProvider($provider, $service);
    }

    public function boot()
    {
        $this->application->boot();
    }

    public function booting($callback)
    {
        $this->application->booting($callback);
    }

    public function booted($callback)
    {
        $this->application->booted($callback);
    }

    public function getCachedCompilePath()
    {
        return $this->application->getCachedCompilePath();
    }

    public function getCachedServicesPath()
    {
        return $this->application->getCachedServicesPath();
    }

    public function bound($abstract)
    {
        return $this->application->bound($abstract);
    }

    public function alias($abstract, $alias)
    {
        $this->application->alias($abstract, $alias);
    }

    public function tag($abstracts, $tags)
    {
        $this->application->tag($abstracts, $tags);
    }

    public function tagged($tag)
    {
        return $this->application->tagged($tag);
    }

    public function bind($abstract, $concrete = null, $shared = false)
    {
        $this->application->bind($abstract, $concrete, $shared);
    }

    public function bindIf($abstract, $concrete = null, $shared = false)
    {
        $this->application->bindIf($abstract, $concrete, $shared);
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->application->singleton($abstract, $concrete);
    }

    public function extend($abstract, \Closure $closure)
    {
        $this->application->extend($abstract, $closure);
    }

    public function instance($abstract, $instance)
    {
        $this->application->instance($abstract, $instance);
    }

    public function when($concrete)
    {
        return $this->application->when($concrete);
    }

    public function make($abstract, array $parameters = [])
    {
        return $this->application->make($abstract, $parameters);
    }

    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
        return $this->application->call($callback, $parameters, $defaultMethod);
    }

    public function resolved($abstract)
    {
        return $this->application->resolved($abstract);
    }

    public function resolving($abstract, \Closure $callback = null)
    {
        $this->application->resolving($abstract, $callback);
    }

    public function afterResolving($abstract, \Closure $callback = null)
    {
        $this->application->afterResolving($abstract, $callback);
    }
}

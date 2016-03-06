<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Definition;

/**
 * This definition is a simple object representing a service. It encapsulate the data required to instantiate the
 * service and register it to the Application container.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ServiceInterface
{
    /**
     * @return string Name of the service
     */
    public function getName();

    /**
     * @return string Class of the service
     */
    public function getClass();

    /**
     * @return string[]|Reference[] Arguments required to instantiate the service
     */
    public function getArguments();

    /**
     * @return string[] Classes/services to which the service is bound to
     */
    public function getAutowiringTypes();

    /**
     * @return array Service tags
     */
    public function getTags();
}

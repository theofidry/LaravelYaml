<?php

/*
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml;

use Fidry\LaravelYaml\Exception\Exception;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface FileLoaderInterface
{
    /**
     * @param string $resource File name or path
     *
     * @return $this
     * @throws Exception
     */
    public function load($resource);
}

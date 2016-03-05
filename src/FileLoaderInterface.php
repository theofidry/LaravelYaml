<?php

namespace Fidry\LaravelYaml;

use Fidry\LaravelYaml\Exception\Exception;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface FileLoaderInterface
{
    /**
     * @param string $fileName File name
     *
     * @return $this
     * @throws Exception
     */
    public function load($fileName);
}

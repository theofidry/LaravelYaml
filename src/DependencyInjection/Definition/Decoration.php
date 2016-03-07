<?php

/**
 * This file is part of the LaravelYaml package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\LaravelYaml\DependencyInjection\Definition;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class Decoration implements DecorationInterface
{
    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @var string
     */
    private $decorates;

    /**
     * @var string
     */
    private $decorationInnerName;

    /**
     * @param ServiceInterface $service
     * @param string $decorates
     * @param string           $decorationInnerName
     */
    public function __construct(ServiceInterface $service, $decorates, $decorationInnerName = null)
    {
        $this->service = $service;
        $this->decorates = $decorates;
        $this->decorationInnerName = (null === $decorationInnerName) ?
            sprintf('%s.inner', $service->getName())
            : $decorationInnerName
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->service->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->service->getClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->service->getArguments();
    }

    /**
     * {@inheritdoc}
     */
    public function getAutowiringTypes()
    {
        return $this->service->getAutowiringTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->service->getTags();
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorates()
    {
        return $this->decorates;
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorationInnerName()
    {
        return $this->decorationInnerName;
    }
}

<?php
declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait GetContainerInstance
{
    protected function getContainerInstance(): ContainerInterface
    {
        /** @var ContainerInterface|null $container */
        $container = static::getContainer();

        if (null === $container) {
            throw new \RuntimeException('Container not found. Did you include "parent::setUp()"?');
        }

        return $container;
    }
}

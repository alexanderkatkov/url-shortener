<?php
declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait GetEntityManager
{
    abstract protected function getContainerInstance(): ContainerInterface;

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainerInstance()
            ->get('doctrine')
            ->getManager();
    }
}

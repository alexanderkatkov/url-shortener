<?php
declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

trait DatabasePurger
{
    abstract protected function getEntityManager(): EntityManagerInterface;

    protected function purgeDb(): void
    {
        $purger = new ORMPurger($this->getEntityManager());

        $purger->purge();
    }
}

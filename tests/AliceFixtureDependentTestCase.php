<?php
declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\Loader\NativeLoader;
use App\Tests\Traits\DatabasePurger;
use App\Tests\Traits\GetContainerInstance;
use App\Tests\Traits\GetEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use RuntimeException;

abstract class AliceFixtureDependentTestCase extends ApiTestCase
{
    use GetEntityManager, DatabasePurger, GetContainerInstance;

    private const FIXTURE_FOLDER_PARAM_NAME = 'fixtures_base_path';

    private array $fixtures = [];
    private string $fixturesBasePath;
    protected EntityManagerInterface $entityManager;
    private NativeLoader $loader;

    protected function setUp(): void
    {
        $this->fixturesBasePath = $this->getFixturesBasePath();
        $this->entityManager = $this->getEntityManager();
        $this->loader = new NativeLoader($this->getContainerInstance());

        $this->purgeDb();
        $this->loadFixtures($this->getFixtureFiles());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->getEntityManager()->getConnection()->close();
        self::ensureKernelShutdown();
    }

    private function loadFixtures(array $fixtureFiles): void
    {
        $objectSet = $this->loadFixtureFiles($fixtureFiles);

        foreach ($objectSet as $reference => $object) {
            $this->entityManager->persist($object);

            $this->fixtures[$reference] = $object;
        }

        $this->entityManager->flush();
    }

    private function loadFixtureFiles(array $fixtureFiles): array
    {
        if (empty($fixtureFiles)) {
            return [];
        }

        $files = array_map(
            function ($file): string {
                $handle = $this->fixturesBasePath . ltrim($file);

                if (!file_exists($handle)) {
                    throw new LogicException(sprintf('Fixture file %s not found', $file));
                }

                return $handle;
            },
            $fixtureFiles
        );

        return $this->loader->loadFiles($files)->getObjects();
    }

    protected function getFixturesBasePath(): string
    {
        if (!self::getContainer()->hasParameter(self::FIXTURE_FOLDER_PARAM_NAME)) {
            throw new RuntimeException(sprintf(
                'Parameter "%s" is missing',
                self::FIXTURE_FOLDER_PARAM_NAME
            ));
        }

        return self::getContainer()->getParameter(self::FIXTURE_FOLDER_PARAM_NAME);
    }

    protected function getReference(string $reference): mixed
    {
        if (!isset($this->fixtures[$reference])) {
            throw new RuntimeException(sprintf('Reference to "%s" does not exist', $reference));
        }

        return $this->fixtures[$reference];
    }

    abstract protected function getFixtureFiles(): array;
}

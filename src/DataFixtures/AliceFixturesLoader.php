<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Loader\NativeLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class AliceFixturesLoader extends Fixture
{
    private const FIXTURE_FOLDER_PARAM_NAME = 'fixtures_base_path';

    private Finder $finder;
    private NativeLoader $loader;

    public function __construct(
        private ParameterBagInterface $params,
        ContainerInterface $container,
    ) {
        $this->finder = new Finder();
        $this->loader = new NativeLoader($container);
    }

    public function load(ObjectManager $manager): void
    {
        $fixtureFiles = $this->findFixtureFiles();
        $objectSet = $this->loader->loadFiles($fixtureFiles)->getObjects();

        foreach ($objectSet as $reference => $object) {
            $manager->persist($object);

            $this->addReference($reference, $object);
        }

        $manager->flush();
    }

    private function findFixtureFiles(): array
    {
        if (!$this->params->has(self::FIXTURE_FOLDER_PARAM_NAME)) {
            throw new RuntimeException(sprintf(
                'Parameter "%s" is missing',
                self::FIXTURE_FOLDER_PARAM_NAME
            ));
        }

        $this->finder
            ->in($this->params->get(self::FIXTURE_FOLDER_PARAM_NAME))
            ->files()
            ->name(['*.yml', '*.yaml']);

        if (!$this->finder->hasResults()) {
            return [];
        }

        return array_map(
            fn($file) => $file->getRealPath(),
            iterator_to_array($this->finder->getIterator())
        );
    }
}

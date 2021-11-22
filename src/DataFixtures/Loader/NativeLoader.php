<?php
declare(strict_types=1);

namespace App\DataFixtures\Loader;

use App\DataFixtures\Faker\Provider\LocalesProvider;
use App\DataFixtures\Faker\Provider\MoneyProvider;
use Faker\Factory as FakerGeneratorFactory;
use Faker\Generator as FakerGenerator;
use Nelmio\Alice\Faker\Provider\AliceProvider;
use Nelmio\Alice\Loader\NativeLoader as BaseNativeLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NativeLoader extends BaseNativeLoader
{
    public function __construct(
        private ContainerInterface $container,
        FakerGenerator $fakerGenerator = null,
    ) {
        parent::__construct($fakerGenerator);
    }

    protected function createFakerGenerator(): FakerGenerator
    {
        $generator = FakerGeneratorFactory::create();
        $generator->addProvider(new AliceProvider());
        $generator->seed($this->getSeed());

        return $generator;
    }
}

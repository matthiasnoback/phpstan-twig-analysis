<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Symfony;

use PHPStan\Testing\PHPStanTestCase;
use PhpStanTwigAnalysis\Twig\Symfony\SymfonyContainerTwigFactory;
use PhpStanTwigAnalysis\Twig\TwigFactory;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

final class LoadingTwigEnvironmentFromSymfonyContainerTest extends PHPStanTestCase
{
    private string $symfonyCacheDir;

    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->symfonyCacheDir = __DIR__ . '/var';

        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->symfonyCacheDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->symfonyCacheDir);
    }

    public function test(): void
    {
        // This ensures that the container .php file exists
        $kernel = new AppKernel($this->symfonyCacheDir);
        $kernel->boot();
        $kernel->getContainer();

        // We can now try to load Twig from the Symfony container
        /** @var TwigFactory $twigFactory */
        $twigFactory = self::getContainer()->getByType(TwigFactory::class);
        self::assertInstanceOf(SymfonyContainerTwigFactory::class, $twigFactory);
        self::assertInstanceOf(Environment::class, $twigFactory->create());
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../config/extension.neon',
            __DIR__ . '/../../config/symfony.neon',
            __DIR__ . '/phpstan.neon',
        ];
    }
}

<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

final class TemplateFilePaths
{
    public function __construct(private TwigFactory $twigFactory)
    {
    }

    /**
     * @return array<string>>
     */
    public function collectAll(): array
    {
        $twig = $this->twigFactory->create();

        return $this->collectTwigFilePathsFromLoader($twig->getLoader());
    }

    /**
     * @return array<string>>
     */
    private function collectTwigFilePathsFromLoader(LoaderInterface $loader): array
    {
        $directories = [];

        if ($loader instanceof ChainLoader) {
            foreach ($loader->getLoaders() as $subLoader) {
                $directories = array_merge($directories, $this->collectTwigFilePathsFromLoader($subLoader));
            }
        } elseif ($loader instanceof FilesystemLoader) {
            foreach ($loader->getNamespaces() as $namespace) {
                foreach ($loader->getPaths($namespace) as $path) {
                    $directories[] = $path;
                }
            }
        }

        return array_map(
            fn (SplFileInfo $fileInfo) => $fileInfo->getRealPath(),
            iterator_to_array(Finder::create()->in($directories)->name('*.twig')->files())
        );
    }
}

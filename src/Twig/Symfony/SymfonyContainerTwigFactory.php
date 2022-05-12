<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig\Symfony;

use PhpStanTwigAnalysis\Twig\TwigFactory;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

final class SymfonyContainerTwigFactory implements TwigFactory
{
    public function __construct(
        private string $containerPhpPath,
        private string $serviceName,
    ) {
        if (! is_file($this->containerPhpPath)) {
            throw new RuntimeException(sprintf('Container path %s does not ', $this->containerPhpPath));
        }
    }

    public function create(): Environment
    {
        /** @var ContainerInterface $container */
        $container = require $this->containerPhpPath;

        if (! $container->has($this->serviceName)) {
            throw new RuntimeException(
                sprintf('Symfony container has no service "%s", maybe it is private', $this->serviceName)
            );
        }
        $twig = $container->get($this->serviceName);
        if (! $twig instanceof Environment) {
            throw new RuntimeException(
                sprintf('Symfony service "%s" should be an instance of %s', $this->serviceName, Environment::class,)
            );
        }

        return $twig;
    }
}

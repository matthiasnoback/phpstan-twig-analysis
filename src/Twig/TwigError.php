<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Source;

final class TwigError
{
    public function __construct(
        private string $error,
        private ?Source $source,
        private int $line,
    ) {
    }

    public function path(): string
    {
        if ($this->source === null) {
            return '?';
        }

        $path = $this->source->getPath() ?: $this->source->getName();

        $cwd = (getcwd() ?: '.') . '/';
        if (str_starts_with($path, $cwd)) {
            return substr($path, strlen($cwd));
        }

        return $path;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function error(): string
    {
        return $this->error;
    }
}

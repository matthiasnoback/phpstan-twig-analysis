<?php
declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use Twig\Node\Node;

final class IncludedFrom
{
    public function __construct(
        private string $file,
        private int $line,
    )
    {
    }

    public static function twigNode(Node $node): self
    {
        return new self($node->getTemplateName() ?: 'unknown', $node->getTemplateLine());
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }
}

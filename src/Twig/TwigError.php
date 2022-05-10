<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Source;

final class TwigError
{
    private function __construct(
        private string $error,
        private ?Source $source,
        private int $line,
    ) {
    }

    public static function createForNode(Node $node, string $message): self
    {
        return new self($message, $node->getSourceContext(), $node->getTemplateLine(),);
    }

    public static function createFromSyntaxError(SyntaxError $error): self
    {
        return new self($error->getMessage(), $error->getSourceContext(), $error->getLine());
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

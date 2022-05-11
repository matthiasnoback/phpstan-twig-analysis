<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Source;

final class TwigError
{
    private function __construct(
        private string $error,
        private ?Source $source,
        private int $line,
        private ?string $tip = null,
    ) {
    }

    public static function createForNode(Node $node, string $message, ?string $tip = null): self
    {
        return new self($message, $node->getSourceContext(), $node->getTemplateLine(), $tip);
    }

    public static function createFromSyntaxError(SyntaxError $error): self
    {
        return new self($error->getMessage(), $error->getSourceContext(), $error->getLine());
    }

    public function line(): int
    {
        return $this->line;
    }

    public function error(): string
    {
        return $this->error;
    }

    public function asPhpStanError(): RuleError
    {
        $phpstanError = RuleErrorBuilder::message(
            sprintf('%s, in %s:%d', $this->error, $this->path(), $this->line),
        )->metadata([
            'template_file' => $this->path(),
            'template_line' => $this->line(),
        ]);

        if ($this->tip !== null) {
            $phpstanError->tip($this->tip);
        }

        return $phpstanError->build();
    }

    private function path(): string
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
}

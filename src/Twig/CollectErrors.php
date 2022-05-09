<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use Twig\Environment;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

final class CollectErrors implements NodeVisitorInterface
{
    /**
     * @var array<TwigError>
     */
    private array $errors = [];

    /**
     * @param array<TwigRule> $rules
     */
    public function __construct(private array $rules)
    {
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        foreach ($this->rules as $rule) {
            if (is_a($node, $rule->getNodeType())) {
                foreach ($rule->processNode($node) as $error) {
                    $this->errors[] = $error;
                };
            }
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 10;
    }

    /**
     * @return array<TwigError>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}

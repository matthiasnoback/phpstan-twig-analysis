<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\Twig;

use PhpStanTwigAnalysis\PhpStan\IncludedFrom;
use PhpStanTwigAnalysis\PhpStan\IncludedTemplate;
use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\ImportNode;
use Twig\Node\IncludeNode;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

final class CollectIncludes implements NodeVisitorInterface
{
    /**
     * @var array<IncludeNode>
     */
    private array $includeNodes = [];

    /**
     * @var array<ModuleNode>
     */
    private array $moduleNodes = [];

    /**
     * @var array<ImportNode>
     */
    private array $importNodes = [];

    /**
     * @return array<IncludedTemplate>
     */
    public function includedTemplates(): array
    {
        $includedTemplates = array_map(
            fn (ConstantExpression $expr): IncludedTemplate => new IncludedTemplate(
                IncludedFrom::twigNode($expr),
                $expr->getAttribute('value')
            ),
            array_filter(
                array_map(fn (IncludeNode $node): Node => $node->getNode('expr'), $this->includeNodes),
                fn (Node $expr): bool => $expr instanceof ConstantExpression,
            )
        );

        $extendedTemplates = array_map(
            fn (ConstantExpression $expr): IncludedTemplate => new IncludedTemplate(
                IncludedFrom::twigNode($expr),
                $expr->getAttribute('value')
            ),
            array_filter(
                array_map(fn (ModuleNode $node): Node => $node->getNode('parent'), $this->moduleNodes),
                fn (Node $expr): bool => $expr instanceof ConstantExpression,
            )
        );

        $importedTemplates = array_map(
            fn (ConstantExpression $expr): IncludedTemplate => new IncludedTemplate(
                IncludedFrom::twigNode($expr),
                $expr->getAttribute('value')
            ),
            array_filter(
                array_map(fn (ImportNode $node): Node => $node->getNode('expr'), $this->importNodes),
                fn (Node $expr): bool => $expr instanceof ConstantExpression,
            )
        );

        return array_merge($includedTemplates, $extendedTemplates, $importedTemplates);
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof IncludeNode) {
            $this->includeNodes[] = $node;
        }

        if ($node instanceof ModuleNode && $node->hasNode('parent')) {
            $this->moduleNodes[] = $node;
        }

        if ($node instanceof ImportNode) {
            $this->importNodes[] = $node;
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
}

<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<MethodCall, array{string,int}>
 */
final class CollectTwigTemplateNames implements Collector
{
    public function __construct(private ExtractTemplateName $extractTemplateName)
    {
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return null|array{string,int}
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        $templateName = $this->extractTemplateName->fromMethodCall($node, $scope);
        if ($templateName === null) {
            return null;
        }

        return [$templateName, $node->getLine()];
    }
}

<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

/**
 * @implements Collector<MethodCall, array{string,int}>
 */
final class CollectTwigTemplateNames implements Collector
{
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
        $templateName = $this->extractTemplateNameFrom($node, $scope);
        if ($templateName === null) {
            return null;
        }

        return [$templateName, $node->getLine()];
    }

    private function isCallToTwigRender(MethodCall $node, Type $objectType): bool
    {
        if ((new ObjectType(Environment::class))
            ->isSuperTypeOf($objectType)
            ->yes()) {
            return $node->name instanceof Identifier && $node->name->toString() === 'render';
        }

        if ((new ObjectType(AbstractController::class))
            ->isSuperTypeOf($objectType)
            ->yes()) {
            return $node->name instanceof Identifier &&
                ($node->name->toString() === 'render' || $node->name->toString() === 'renderView');
        }

        return false;
    }

    private function extractTemplateNameFrom(MethodCall $node, Scope $scope): ?string
    {
        // TODO extract service

        if (! $this->isCallToTwigRender($node, $scope->getType($node->var))) {
            return null;
        }

        if (! isset($node->getArgs()[0])) {
            // The method call has no arguments
            return null;
        }

        $firstArgument = $node->getArgs()[0];
        $firstArgumentType = $scope->getType($firstArgument->value);
        if (! $firstArgumentType instanceof ConstantStringType) {
            // The first argument is not a constant string
            return null;
        }

        return $firstArgumentType->getValue();
    }
}

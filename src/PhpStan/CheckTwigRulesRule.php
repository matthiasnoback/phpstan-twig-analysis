<?php

declare(strict_types=1);

namespace PhpStanTwigAnalysis\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PhpStanTwigAnalysis\Twig\TwigAnalyzer;
use PhpStanTwigAnalysis\Twig\TwigError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

/**
 * @implements Rule<MethodCall>
 */
final class CheckTwigRulesRule implements Rule
{
    public function __construct(
        private TwigAnalyzer $twigAnalyzer,
    ) {
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->isCallToTwigRender($node, $scope->getType($node->var))) {
            return [];
        }

        if (! isset($node->getArgs()[0])) {
            // The method call has no arguments
            return [];
        }

        $firstArgument = $node->getArgs()[0];
        $firstArgumentType = $scope->getType($firstArgument->value);
        if (! $firstArgumentType instanceof ConstantStringType) {
            // The first argument is not a constant string
            return [];
        }

        $templateName = $firstArgumentType->getValue();

        $twigErrors = $this->twigAnalyzer->analyze($templateName);

        return array_map(
            fn (TwigError $twigError) => RuleErrorBuilder::message(
                sprintf('%s, in %s:%d', $twigError->error(), $twigError->path(), $twigError->line()),
            )->metadata([
                'template_file' => $twigError->path(),
                'template_line' => $twigError->line(),
            ])
                ->build(),
            $twigErrors
        );
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
}

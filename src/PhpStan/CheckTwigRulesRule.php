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
use PhpStanTwigAnalysis\Twig\CollectErrors;
use PhpStanTwigAnalysis\Twig\TwigRule;
use Twig\Environment;
use Twig\NodeTraverser;

/**
 * @implements Rule<MethodCall>
 */
final class CheckTwigRulesRule implements Rule
{
    /**
     * @param array<TwigRule> $twigRules
     */
    public function __construct(
        private Environment $twig,
        private array $twigRules,
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
        $twigEnvironment = new ObjectType(Environment::class);
        if (! $twigEnvironment
            ->isSuperTypeOf($scope->getType($node->var))
            ->yes()) {
            // The object is not a Twig `Environment` instance
            return [];
        }
        if (! $node->name instanceof Identifier) {
            // The method is called dynamically, or is not `render()`
            return [];
        }
        if ($node->name->toString() !== 'render') {
            // The method is called dynamically, or is not `render()`
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

        $source = $this->twig->getLoader()
            ->getSourceContext($templateName);

        $collectErrors = new CollectErrors($this->twigRules);

        $nodeTree = $this->twig->parse($this->twig->tokenize($source));

        $nodeTraverser = new NodeTraverser($this->twig, [$collectErrors]);
        $nodeTraverser->traverse($nodeTree);

        $phpstanErrors = [];

        foreach ($collectErrors->errors() as $twigError) {
            $phpstanErrors[] = RuleErrorBuilder::message(
                sprintf('%s, in %s:%d', $twigError->error(), $twigError->path(), $twigError->line()),
            )->metadata([
                'template_file' => $twigError->path(),
                'template_line' => $twigError->line(),
            ])
                ->build();
        }

        return $phpstanErrors;
    }
}

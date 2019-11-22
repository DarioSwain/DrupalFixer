<?php

/*
 * This file is part of Drupal Fixer.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * This source file is subject to the GPL-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DS\DrupalFixer\Rector\D80;

use phpDocumentor\Reflection\Types\Self_;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Rector\CodingStyle\Naming\ClassNaming;
use Rector\Exception\Bridge\RectorProviderException;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\ConfiguredCodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Class TransformGlobalFunctionsRector
 * @package DS\DrupalFixer\Rector\D80
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
final class TransformGlobalFunctionsRector extends AbstractRector
{
    const TYPE_STATIC = 'static';
    const TYPE_DI = 'di';
    const TYPE_AUTO = 'auto';
    const TYPE_TRAIT = 'trait';

    /** @var array */
    private $transformGlobalFunctions = [];

    /**
     * SetMessageRector constructor.
     * @param array $transformGlobalFunctions e.g. ["drupal_set_message" => ["Drupal", "render"]]
     */
    public function __construct(array $transformGlobalFunctions = [])
    {
        $this->transformGlobalFunctions = $transformGlobalFunctions;
    }

    /** {@inheritDoc} */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Transform global functions to OOP based API.', [
            new ConfiguredCodeSample(
                'drupal_set_message("...", "...");',
                '\Drupal::messenger()->addMessage("...", "...");',
                [
                    'drupal_set_message' => [
                        'replacementType' => 'static', // 'auto', 'di', 'trait',
                        'static' => [
                            'class' => 'Drupal',
                            'factoryMethod' => 'messenger',
                            'instanceMethod' => 'addMessage'
                        ],
                        'di' => [ //TBD
                            'injectedServiceClassName' => '',
                            'propertyName' => '',
                            'serviceMethod' => '',
                        ],
                        'trait' => [ //TBD
                            'traitClassName' => '',
                            'traitMethod' => '',
                        ]
                    ]
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param Node $node
     * @return Node|null
     * @throws RectorProviderException
     */
    public function refactor(Node $node): ?Node
    {
//        $class = $node->getAttribute(AttributeKey::CLASS_NODE);
//        if ($class instanceof Node\Stmt\Class_) {
//            var_dump($this->getName($class));die;
//        }

        if (!$node instanceof FuncCall) {
            return null;
        }

        if (!$node->name instanceof Name) {
            return null;
        }

        $functionName = $this->getName($node);
        if (!array_key_exists($functionName, $this->transformGlobalFunctions)) {
            return null;
        }
        $transformFunctionConfiguration = $this->transformGlobalFunctions[$functionName];

        switch ($transformFunctionConfiguration['replacementType']) {
            case 'static':
                $this->exceptionOnNotFoundOrEmpty($transformFunctionConfiguration, self::TYPE_STATIC);

                return $this->replaceWithMethodFromStaticFactory($node, $transformFunctionConfiguration[self::TYPE_STATIC]);
            case 'di':
                break;
            case 'trait':
                break;
            case 'auto':
                break;
            default:
                throw new RectorProviderException(sprintf(
                    'Replacement type configuration was not provided for function: "%s".',
                    $functionName
                ));
        }

        return null;
    }

    protected function replaceWithMethodFromStaticFactory(FuncCall $functionNode, array $configuration): ?Node
    {
        $factoryMethodParams = !empty($configuration['factoryMethodParams']) ? $this->wrapToArg($configuration['factoryMethodParams']) : [];
        $node = $this->createStaticCall($configuration['class'], $configuration['factoryMethod'], $factoryMethodParams);
        $nextMethodConfigurations = isset($configuration['nextMethod']) ? $configuration['nextMethod'] : '' ;

        while (!empty($nextMethodConfigurations)){
            $node = $this->createMethodCall( $node, $nextMethodConfigurations['methodName'], $nextMethodConfigurations['methodParams']);
            $nextMethodConfigurations = isset($nextMethodConfigurations['nextMethod']) ? $nextMethodConfigurations['nextMethod'] : '' ;
        }

        $node = $this->createMethodCall(
            $node,
            $configuration['instanceMethod'],
            $functionNode->args
        );

        return $node;
    }

    /**
     * @param array $configuration
     * @param string $type
     * @throws RectorProviderException
     */
    protected function validateConfigurationNode(array $configuration, string $type = self::TYPE_STATIC)
    {
        switch ($type) {
            case self::TYPE_STATIC:
                $this->exceptionOnNotFoundOrEmpty($configuration, 'class');
                $this->exceptionOnNotFoundOrEmpty($configuration, 'factoryMethod');
                $this->exceptionOnNotFoundOrEmpty($configuration, 'instanceMethod');
                break;
            case self::TYPE_DI:
                $this->exceptionOnNotFoundOrEmpty($configuration, 'injectedServiceClassName');
                $this->exceptionOnNotFoundOrEmpty($configuration, 'propertyName');
                $this->exceptionOnNotFoundOrEmpty($configuration, 'serviceMethod');
                break;
            case self::TYPE_TRAIT:
                $this->exceptionOnNotFoundOrEmpty($configuration, 'traitClassName');
                $this->exceptionOnNotFoundOrEmpty($configuration, 'traitMethod');
                break;
            case self::TYPE_AUTO:
                break;
        }
    }

    /**
     * Validation method, throws exception in case when configuration node not found or empty.
     * @param array $configuration
     * @param string $key
     * @throws RectorProviderException
     */
    protected function exceptionOnNotFoundOrEmpty(array $configuration, string $key)
    {
        if (!array_key_exists($key, $configuration)) {
            throw new RectorProviderException(sprintf('Required "%s" configuration parameter was not found.', $key));
        }

        if (empty($configuration[$key])) {
            throw new RectorProviderException(sprintf('Required "%s" configuration parameter contains empty value.', $key));
        }
    }

    /**
     * @param Expr[]|Arg[] $args
     * @return Arg[]
     */
    private function wrapToArg(array $args): array
    {
        foreach ($args as $key => $arg) {
            if ($arg instanceof Arg) {
                continue;
            }
            $args[$key] = new String_($arg);
        }
        return $args;
    }
}

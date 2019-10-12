<?php

/*
 * This file is part of Drupal Fixer.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * This source file is subject to the GPL-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DS\DrupalFixer\Example;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\ConfiguredCodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Class SetMessageRector
 * @package DS\DrupalFixer\Example
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
final class SetMessageRector extends AbstractRector
{
    /** @var array */
    private $rectorConfiguration = [];

    /** @param array $rectorConfiguration e.g. ["drupal_set_message" => ["Drupal", "messenger"]] */
    public function __construct(array $rectorConfiguration = [])
    {
        $this->rectorConfiguration = $rectorConfiguration;
    }

    /** @return RectorDefinition */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition(
            'Turns drupal_set_message to Messenger API.',
            [
                new ConfiguredCodeSample(
                    'drupal_set_message("...", "...");',
                    '\Drupal::messenger()->addMessage("...", "...");',
                    []
                ),
            ]
        );
    }

    /** @return string[] */
    public function getNodeTypes(): array
    {
        // In our example we need to find all function calls. like drupal_set_message(), implode(), strlen(), etc...
        return [FuncCall::class];
    }

    /**
     * @param Node $node
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        // We are checking that provided node is of FuncCall type. In our example it's not required to do, because we're catching only FuncCall nodes.
        if (!$node instanceof FuncCall) {
            // Returning null means that we're not doing anythig with provided Node.
            return null;
        }

        // Checking that it's not an anonymous function.
        if (!$node->name instanceof Name) {
            return null;
        }

        // Extracting function name
        $functionName = $this->getName($node);

        // Checking that function name is equal to 'drupal_set_message'
        if ($functionName !== 'drupal_set_message') {
            return null;
        }

        // Replace current node with new one created by createMethodCall method call.
        // Resulted node will be converted to: (object)->addMessage(paramFromOriginalNode1, paramFromOriginalNode2)
        return $this->createMethodCall(
            // Create new static method call with empty arguments. e.g \Drupal::messenger();
            $this->createStaticCall('Drupal', 'messenger', []),
            'addMessage',
            $node->args
        );
    }
}

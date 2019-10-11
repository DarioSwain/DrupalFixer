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

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\ConfiguredCodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Class SetMessageRector
 * @package DS\DrupalFixer\Rector\D80
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
final class SetMessageRector extends AbstractRector
{
    /**
     * @var string[]
     */
    private $functionToMethodCall = [];

    /**
     * @param string[] $functionToMethodCall e.g. ["view" => ["this", "render"]]
     */
    public function __construct(array $functionToMethodCall = [])
    {
        $this->functionToMethodCall = $functionToMethodCall;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Turns drupal_set_message to Messenger API.', [
            new ConfiguredCodeSample(
                'drupal_set_message("...", "...");',
                '\Drupal::messenger()->addMessage("...", "...");',
                [
                //                    'view' => ['this', 'render'],
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
     * @param FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->name instanceof Name) {
            return null;
        }

        $functionName = $this->getName($node);
        if ($functionName !== 'drupal_set_message') {
            return null;
        }

        /** @var Identifier $identifier */
        $identifier = $node->name;
        $functionName = $identifier->toString();

        return $this->createMethodCall(
            $this->createStaticCall('Drupal', 'messenger', []),
            'addMessage',
            $node->args
        );
    }
}

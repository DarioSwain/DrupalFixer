<?php

/*
 * This file is part of Drupal Fixer.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * This source file is subject to the GPL-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DS\DrupalFixer\Fixer;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Class SetMessageFixer
 * @package DS\DrupalFixer\Fixer
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
final class SetMessageFixer implements DefinedFixerInterface
{

    /** {@inheritDoc} **/
    public function getDefinition()
    {
        // TODO: Implement getDefinition() method.
    }

    /** {@inheritDoc} **/
    public function isCandidate(Tokens $tokens)
    {
        // TODO: Implement isCandidate() method.
    }

    /** {@inheritDoc} **/
    public function isRisky()
    {
        // TODO: Implement isRisky() method.
    }

    /** {@inheritDoc} **/
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // TODO: Implement fix() method.
    }

    /** {@inheritDoc} **/
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /** {@inheritDoc} **/
    public function getPriority()
    {
        // TODO: Implement getPriority() method.
    }

    /** {@inheritDoc} **/
    public function supports(\SplFileInfo $file)
    {
        // TODO: Implement supports() method.
    }
}

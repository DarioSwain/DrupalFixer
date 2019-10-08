<?php

/*
 * This file is part of Drupal Fixer.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * This source file is subject to the GPL-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DS\DrupalFixer\Tests\Fixer;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * @internal
 *
 * @covers \DS\DrupalFixer\Fixer\SetMessageFixer
 */
final class SetMessageFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [];
    }
}

<?php

/*
 * This file is part of Drupal Fixer.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * This source file is subject to the GPL-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DS\DrupalFixer\Tests\Test;

use \PhpCsFixer\Tests\Test\AbstractFixerTestCase as BaseAbstractFixerTestCase;

/**
 * Class AbstractFixerTestCase
 * @package DS\DrupalFixer\Tests\Test
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * @internal
 */
class AbstractFixerTestCase extends BaseAbstractFixerTestCase
{
    /** {@inheritDoc} **/
    protected function createFixer()
    {
        $fixerClassName = preg_replace('/^(DS\\\\DrupalFixer)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);

        return new $fixerClassName();
    }
}

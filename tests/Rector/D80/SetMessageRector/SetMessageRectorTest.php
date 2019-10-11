<?php declare(strict_types=1);

/*
 * This file is part of Drupal Fixer.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * This source file is subject to the GPL-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DS\DrupalFixer\Tests\Rector\D80\SetMessageRector;

use DS\DrupalFixer\Rector\D80\SetMessageRector;
use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * Class SetMessageRectorTest
 * @package DS\DrupalFixer\Tests\Rector\D80\SetMessageRector
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
final class SetMessageRectorTest extends AbstractRectorTestCase
{
    /**
     * @param string $file
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc'];
    }

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            SetMessageRector::class => []
        ];
    }
}

<?php declare(strict_types=1);

/*
 * This file is part of Drupal Fixer.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * This source file is subject to the GPL-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DS\DrupalFixer\Tests\Rector\D80\TransformGlobalFunctionsRector;

use DS\DrupalFixer\Rector\D80\TransformGlobalFunctionsRector;
use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * Class TransformGlobalFunctionsRectorTest
 * @package DS\DrupalFixer\Tests\Rector\D80\TransformGlobalFunctionsRector
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
final class TransformGlobalFunctionsRectorTest extends AbstractRectorTestCase
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
        yield [__DIR__ . '/Fixture/in_class_fixture.php.inc'];
    }

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            TransformGlobalFunctionsRector::class => [
                '$transformGlobalFunctions' => [
                    'drupal_set_message' => [
                        'replacementType' => 'static',
                        'static' => [
                            'class' => 'Drupal',
                            'factoryMethod' => 'messenger',
                            'instanceMethod' => 'addMessage'
                        ]
                    ],
                    'db_select' => [
                        'replacementType' => 'static',
                        'static' => [
                            'class' => 'Drupal',
                            'factoryMethod' => 'database',
                            'instanceMethod' => 'select'
                        ]
                    ],
                ],
            ],
        ];
    }
}

<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-js-html-optimizer
 */

declare(strict_types=1);

namespace Abeliani\CssJsHtmlOptimizer\Tests;

use Abeliani\CssJsHtmlOptimizer\Js;
use Codeception\Test\Unit;

class JsOptimizeTest extends Unit
{
    private string $js;

    protected function setUp(): void
    {
        $this->js = file_get_contents(
            codecept_data_dir('script.js')
        );

        parent::setUp();
    }

    public function testRenameFunction(): void
    {
        $document = new Js\Parser\Document($this->js);
        $optimizer = new Js\Optimizer\Optimizer($document);

        $optimized = $optimizer->do()->flush();
        $this->assertStringNotContainsString('factorial', $optimized);
        $this->assertStringContainsString('function o_1(n){if(n===0||n===1){return 1}return n*o_1(n-1)}', $optimized);
    }

    public function testRenameFunctionNoConflict(): void
    {
        $document = new Js\Parser\Document(
            'function o_1 (n) {
                if (n === 0 || n === 1) {
                    return 1;
                }
                return n * o_1(n - 1);
            }
            o_1(5);'
        );

        $optimizer = new Js\Optimizer\Optimizer($document);

        $optimized = $optimizer->do()->flush();
        $this->assertStringContainsString('q_1', $optimized);
        $this->assertStringNotContainsString('o_1', $optimized);
    }

    public function testManyRenamed(): void
    {
        $document = new Js\Parser\Document($this->js);
        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertStringContainsString('o_1(', $optimized);
        $this->assertStringContainsString('o_2(', $optimized);
        $this->assertStringContainsString('o_3(', $optimized);
        $this->assertStringNotContainsString('printer(', $optimized);
        $this->assertStringNotContainsString('factorial(', $optimized);
        $this->assertStringNotContainsString('fibonacci(', $optimized);
    }

    public function testNoConflictRenamed(): void
    {
        $document = new Js\Parser\Document(
            'function o_1 (n) { console.log(123) }
            function q_1() {o_1(5);};'
        );

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertStringContainsString('u_1(', $optimized);
        $this->assertStringContainsString('u_2(', $optimized);
        $this->assertStringNotContainsString('o_1(', $optimized);
        $this->assertStringNotContainsString('q_1(', $optimized);
        $this->assertEquals('function u_1(n){console.log(123)}function u_2(){u_1(5)}', $optimized);
    }
}

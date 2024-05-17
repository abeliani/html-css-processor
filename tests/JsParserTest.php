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

class JsParserTest extends Unit
{
    public function testWrongCode(): void
    {
        $document = new Js\Parser\Document('
                     /\_/\
                    ( o.o )
                     > ^ <
        ');

        $this->assertEmpty($document->parse());
    }

    public function testCompress(): void
    {
        $document = new Js\Parser\Document(
            'function factorial (n) {
                if (n === 0 || n === 1) {
                    return 1;
                }
                return n * factorial(n - 1);
            }'
        );

        $stmt = 'function factorial(n){if(n===0||n===1){return 1}return n*factorial(n-1)}';
        $this->assertEquals($stmt, $document->parse());
    }

    public function testIsolatedFunc(): void
    {
        $document = new Js\Parser\Document(
            '(function($) {
                    $(document).ready(function() {
                        $(".button").click(function() {
                            alert("done!");
                        });
                    });
                })(jQuery);'
        );

        $stmt = '(function($){$(document).ready(function(){$(".button").click(function(){alert("done!")})})})(jQuery)';
        $this->assertEquals($stmt, $document->parse());
    }
}
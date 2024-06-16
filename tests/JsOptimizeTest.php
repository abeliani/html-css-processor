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
        $this->assertStringContainsString("function o_1(n){if(n===0||n===1){return 1}\nreturn n*o_1(n-1)}", $optimized);
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

    public function testCarefulRemoveWhiteSpaces(): void
    {
        $document = new Js\Parser\Document(
            'var image = list[i]
             fixSize = (image.size / 1000).toFixed(1) * 1'
        );
        $optimizer = new Js\Optimizer\Optimizer($document);

        $optimized = $optimizer->do()->flush();
        $this->assertEquals("var image=list[i]\nfixSize=(image.size/1000).toFixed(1)*1", $optimized);
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
        $this->assertEquals("function u_1(n){console.log(123)}\nfunction u_2(){u_1(5)}", $optimized);
    }

    public function testFirstLetterRenamed(): void
    {
        $document = new Js\Parser\Document(
            'function Test (n) { console.log(123) }
            function best() {O_1(5);};'
        );

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertStringContainsString('O_1(', $optimized);
        $this->assertStringContainsString('o_2(', $optimized);
        $this->assertEquals("function O_1(n){console.log(123)}\nfunction o_2(){O_1(5)}", $optimized);
    }

    public function testWhiteSpaceRemove(): void
    {
        $document = new Js\Parser\Document(
            'window.test = (function(){
                    return  window.test       
                    ||
                        function (cb) {
                            window.setTimeout(cb, 100 / 6);
                        };
            })();'
        );

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $expected = 'window.test=(function(){return window.test||function(cb){window.setTimeout(cb,100/6)}})()';
        $this->assertEquals($expected, $optimized);
    }

    public function testSemicolonAfterFunc(): void
    {
        $document = new Js\Parser\Document(
            'this.util().extend(options, this.defaults)
                this.config'
        );

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $expected = "this.util().extend(options,this.defaults)\nthis.config";
        $this->assertEquals($expected, $optimized);
    }

    public function testIfAndNoBrakes(): void
    {
        $document = new Js\Parser\Document(
            'if (filesCount === 0)
                    console.log("imageList", imageList)'
        );

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $expected = "if(filesCount===0)\nconsole.log(\"imageList\",imageList)";
        $this->assertEquals($expected, $optimized);
    }

    public function testRegexpNotProcessing(): void
    {
        $js = <<<JS
json = json.replace(/("")/g, function (match) {
    var style = _number;
    if (/^"/.test(match)) {
        if (/:$/.test(match)) {
            style = _key;
        } else {
            style = _string;
        }
    } else if (/true|false/.test(match)) {
        style = _boolean;
    } else if (/null/.test(match)) {
        style = _null;
    }
    arr.push(style);
    arr.push('');
    return '%c' + match + '%c';
});

arr.unshift(json);
console.log.apply(console, arr);

return json;
JS;

        $optimizer = new Js\Optimizer\Optimizer(new Js\Parser\Document($js));
        $optimized = $optimizer->do()->flush();

        $this->assertStringContainsString('/("")/g', $optimized);
        $this->assertStringContainsString('if(/^"/.test(match)){', $optimized);
        $this->assertStringContainsString('if(/:$/.test(match)){', $optimized);
        $this->assertStringContainsString("}else if(/true|false/.test(match)){", $optimized);
        $this->assertStringContainsString("}else if(/null/.test(match)){", $optimized);
    }

    public function testSetVarsBulk(): void
    {
        $document = new Js\Parser\Document(
            "var
            arr = [],
            _key = 'color: red';"
        );

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertEquals("var arr=[],_key='color: red'", $optimized);
    }

    public function testSetConst(): void
    {
        $document = new Js\Parser\Document('const MAX_WIDTH = 720');

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertEquals('const MAX_WIDTH=720', $optimized);
    }

    public function testSetVar(): void
    {
        $document = new Js\Parser\Document('let me = 100');

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertEquals('let me=100', $optimized);
    }

    public function testSemicolonBeforeVar(): void
    {
        $document = new Js\Parser\Document(
            'const EDITOR = \'#editor\'
            var codeMirror = {}
            let katex = {}'
        );

        $optimizer = new Js\Optimizer\Optimizer($document);
        $optimized = $optimizer->do()->flush();

        $this->assertEquals("const EDITOR='#editor'\nvar codeMirror={}\nlet katex={}", $optimized);
    }
}

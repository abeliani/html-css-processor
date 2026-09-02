<?php

/**
 * This file is part of the CssJsHtmlOptimizer Project.
 *
 * @package     CssJsHtmlOptimizer
 * @author      Anatolii Belianin <belianianatoli@gmail.com>
 * @license     See LICENSE.md for license information
 * @link        https://github.com/abeliani/css-html-optimizer
 */

declare(strict_types=1);

namespace Abeliani\CssJsHtmlOptimizer\Tests;

use Abeliani\CssJsHtmlOptimizer\Css;
use Codeception\Test\Unit;

/**
 * Whitespace that must survive minification.
 *
 * Every case here used to come out glued, and every one of them failed
 * silently: the browser either dropped the rule without an error or kept it
 * and started matching different elements.
 */
class CssWhitespaceTest extends Unit
{
    /**
     * @dataProvider preservedProvider
     */
    public function testWhitespaceIsPreserved(string $css, string $expected): void
    {
        $document = new Css\Parser\Document($css);
        $rendered = implode('', array_map(
            static fn($block): string => (string) $block,
            $document->parse(),
        ));

        $this->assertEquals($expected, $rendered);
    }

    public function preservedProvider(): array
    {
        return [
            // "and (" glued into the function token "and(": the condition
            // stops parsing and the whole block never applies.
            'media condition' => [
                '@media (max-width: 640px) and (min-height: 830px) { .a { color: red; } }',
                '@media (max-width:640px) and (min-height:830px){.a{color:red}}',
            ],
            'media type and condition' => [
                '@media only screen and (min-width: 100px) { .a { color: red; } }',
                '@media only screen and (min-width:100px){.a{color:red}}',
            ],
            'supports condition' => [
                '@supports (display: grid) and (gap: 1px) { .a { gap: 1px; } }',
                '@supports (display:grid) and (gap:1px){.a{gap:1px}}',
            ],
            'supports negation' => [
                '@supports not (display: grid) { .a { float: left; } }',
                '@supports not (display:grid){.a{float:left}}',
            ],
            'container name' => [
                '@container card (min-width: 400px) { .a { color: red; } }',
                '@container card (min-width:400px){.a{color:red}}',
            ],

            // The descendant combinator IS the whitespace. Dropping it after
            // ")" leaves a valid — and wrong — compound selector.
            'descendant after functional pseudo' => [
                ':not(.a) .b { color: red; }',
                ':not(.a) .b{color:red}',
            ],
            'descendant type selector' => [
                'li:nth-child(2) span { color: red; }',
                'li:nth-child(2) span{color:red}',
            ],
            // Same story in front of ":" — "ul :first-child" is not
            // "ul:first-child".
            'descendant pseudo class' => [
                'ul :first-child { color: red; }',
                'ul :first-child{color:red}',
            ],

            // calc() requires whitespace on both sides of "+" and "-".
            'calc after parenthesis' => [
                '.a { width: calc(var(--x) + 10px); }',
                '.a{width:calc(var(--x) + 10px)}',
            ],
            'calc before parenthesis' => [
                '.a { width: calc(10px + (2px * 3)); }',
                '.a{width:calc(10px + (2px * 3))}',
            ],

            // A quoted value is data: its spaces belong to the author.
            'string keeps its space' => [
                '.a::after { content: ", "; }',
                '.a::after{content:", "}',
            ],
            'string keeps its parenthesis' => [
                '.a::after { content: " (note)"; }',
                '.a::after{content:" (note)"}',
            ],
            'comment sequence inside a string' => [
                '.a::after { content: "/*"; }',
                '.a::after{content:"/*"}',
            ],

            'font source format' => [
                '@font-face { src: url(a.woff2) format("woff2"); }',
                '@font-face{src:url(a.woff2) format("woff2")}',
            ],

            // "@importurl(…)" is one ident — the statement is skipped whole.
            'import keyword' => [
                '@import url("a.css");',
                '@import url("a.css");',
            ],
            'import media query' => [
                '@import url("a.css") screen and (min-width: 100px);',
                '@import url("a.css") screen and (min-width:100px);',
            ],
            // The ";" of a data url belongs to the url, not to the statement.
            'import data url' => [
                '@import url("data:text/css;base64,QQ==") print;',
                '@import url("data:text/css;base64,QQ==") print;',
            ],
        ];
    }

    /**
     * @dataProvider droppedProvider
     */
    public function testWhitespaceIsDropped(string $css, string $expected): void
    {
        $document = new Css\Parser\Document($css);
        $rendered = implode('', array_map(
            static fn($block): string => (string) $block,
            $document->parse(),
        ));

        $this->assertEquals($expected, $rendered);
    }

    public function droppedProvider(): array
    {
        return [
            'around declarations' => [
                ".a {\n    color: red;\n    margin: 0;\n}",
                '.a{color:red;margin:0}',
            ],
            'around selector list' => [
                ".a ,  .b { color: red }",
                '.a,.b{color:red}',
            ],
            'trailing semicolon' => [
                '.a { color: red ; }',
                '.a{color:red}',
            ],
            // A comment produces no token, so it cannot separate anything.
            'comment between selector parts' => [
                '.a/*x*/.b { color: red; }',
                '.a.b{color:red}',
            ],
            'comment around a declaration' => [
                '.a { /* note */ color: red; }',
                '.a{color:red}',
            ],
        ];
    }
}

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

namespace Abeliani\CssJsHtmlOptimizer\Css\Parser;

use Abeliani\CssJsHtmlOptimizer\Common\DocumentAbstract;
use Abeliani\CssJsHtmlOptimizer\Css\Block\RuleEnum;
use Abeliani\CssJsHtmlOptimizer\Css\Parser\Trait\CheckerBlockTrait;
use Abeliani\CssJsHtmlOptimizer\Css\Parser\Trait\PrepareCssTrait;

class Document extends DocumentAbstract
{
    use PrepareCssTrait, CheckerBlockTrait;

    private const OPENED_BRACES = '{';
    private const CLOSED_BRACES = '}';
    private const STARTS_WITH_AT = '@';
    private const SEMICOLON = ';';

    private Element $proto;
    private ?array $document = null;

    public function __construct(string $css)
    {
        $this->proto = new Element;
        parent::__construct($this->prepare($css));
    }

    public function parse(): array
    {
        if ($this->document !== null) {
            return $this->document;
        }

        $document = [];
        $length = strlen($this->data);

        for ($i = 0, $start = $braceCount = 0; $i < $length; $i++) {
            switch ($this->data[$i]) {
                case self::OPENED_BRACES:
                    if ($braceCount === 0) {
                        $selector = trim(substr($this->data, $start, $i - $start));

                        $start = $i + 1;
                    }
                    $braceCount++;
                    break;
                case self::CLOSED_BRACES:
                    $braceCount--;
                    if ($braceCount === 0 && isset($selector)) {
                        $document[] = $this->proto->createRule($selector, substr($this->data, $start, $i - $start));

                        $start = $i + 1;
                    }
                    break;
                case self::STARTS_WITH_AT:
                    if ($this->isImport($i)) {
                        // Everything between "@import" and the ";" is kept as
                        // one piece. Splitting on the first space used to drop
                        // whatever came after the url — the media query of
                        // "@import url(a.css) screen and (min-width:100px)"
                        // went missing, and the stylesheet started applying
                        // everywhere.
                        $stmtEnd = $this->statementEnd($i);
                        $statement = substr($this->data, $i, $stmtEnd - $i);

                        $document[] = $this->proto->createImport(
                            RuleEnum::import->value,
                            trim(substr($statement, strlen(RuleEnum::import->value))),
                        );

                        $i = $stmtEnd;
                        $start = $i + 1;
                        break;
                    } elseif ($this->isFontFace($i)) {
                        $stmtEnd = strpos($this->data, self::CLOSED_BRACES, $i);
                        $selectorStmtEnd = strpos($this->data, self::OPENED_BRACES, $i);

                        $document[] = $this->proto->createRule(
                            substr($this->data, $i, $selectorStmtEnd - $i),
                            substr($this->data, $selectorStmtEnd + 1, $stmtEnd - $selectorStmtEnd - 1)
                        );

                        $i = $stmtEnd;
                        $start = $i + 1;
                    } elseif ($this->isMedia($i) || $this->isKeyFrames($i)) {
                        for($mediaBraceCount = 0, $im = $i; ;$im++) {
                            switch ($this->data[$im]) {
                                case self::OPENED_BRACES:
                                    $mediaBraceCount++;
                                    break;
                                case self::CLOSED_BRACES:
                                    $mediaBraceCount--;
                                    if ($mediaBraceCount === 0) {
                                        $stmtEnd = strpos($this->data, self::OPENED_BRACES, $i) + 1;
                                        $subDocument = new Document(substr($this->data, $stmtEnd, $im - $stmtEnd));

                                        $document[] = $this->proto->createRule(
                                            substr($this->data, $i, $stmtEnd - $i - 1),
                                            $subDocument->parse()
                                        );

                                        break 2;
                                    }
                            }
                        }
                        $i = $im;
                        $start = $i + 1;
                        break;
                    }
            }
        }

        return $this->document = $document;
    }

    /**
     * Offset of the ";" that ends an at-rule statement, skipping over quoted
     * values: the ";" of "@import url('data:text/css;base64,…')" belongs to
     * the url, and stopping there truncated the rule.
     */
    private function statementEnd(int $from): int
    {
        $length = strlen($this->data);

        for ($i = $from; $i < $length; $i++) {
            if ($this->data[$i] === '"' || $this->data[$i] === "'") {
                $i = $this->stringEnd($this->data, $i);

                continue;
            }

            if ($this->data[$i] === self::SEMICOLON) {
                return $i;
            }
        }

        return $length;
    }

    public function clear(): void
    {
        $this->document = $this->data = null;
    }
}

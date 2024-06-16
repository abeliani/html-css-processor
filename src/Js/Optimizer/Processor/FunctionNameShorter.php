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

namespace Abeliani\CssJsHtmlOptimizer\Js\Optimizer\Processor;

use Abeliani\CssJsHtmlOptimizer\Common\ProcessorInterface;

class FunctionNameShorter implements ProcessorInterface
{
    private const PATTERNS = ['o', 'q', 'u', 'e', 'l', 'i'];
    private const LAST_PATTERN = 'php';
    private const FUNCTION_PATTERN = '~function\s+(\w{3,}\s*)~';

    private array $renamed = [];
    private array $replacedNames = [];

    public function __invoke(string &$property): void
    {
        if (!$funcCount = preg_match_all(self::FUNCTION_PATTERN, $property)) {
            return;
        }

        for ($prefix = $this->noConflict($property); 0 < $funcCount; $funcCount--) {
            array_unshift($this->replacedNames, sprintf('%s_%d', $prefix, $funcCount));
        }

        if (empty($this->replacedNames)) {
            throw new \Exception('Fail to determine pattern to rename functions');
        }

        $this->renameFunctionDetermine($property);
        $this->renameFunctionCall($property);
    }

    private function noConflict(string $property): string
    {
        foreach (self::PATTERNS as $prefix) {
            if (!preg_match(sprintf('~%s_\d+~', $prefix), $property)) {
                return $prefix;
            }
        }

        return self::LAST_PATTERN;
    }

    private function renameFunctionDetermine(string &$property): void
    {
        $property = preg_replace_callback(self::FUNCTION_PATTERN, function (array $m) use ($property) {
            [$fn, $original] = explode(' ', $m[0]);

            // we cannot replace a function name if we are sure the name is used only to call the function
            if (preg_match(sprintf('~\b%s\b(?!\(|\.)~', $original), $property)) {
                return $m[0];
            }

            $tmpl = array_shift($this->replacedNames);
            $shortName = ctype_upper($original[0]) ? ucfirst($tmpl) : $tmpl;
            $this->renamed[$original] = $shortName;

            return sprintf('%s %s', $fn, $shortName);
        }, $property);
    }

    private function renameFunctionCall(string &$property): void
    {
        $pattern = '~(?<!function\s)\b%s\b(\.|\()~i';
        $renamed = '%s$1';

        foreach ($this->renamed as $old => $new) {
            $property = preg_replace(
                sprintf($pattern, preg_quote($old)),
                sprintf($renamed, $new),
                $property
            );
        }
    }
}

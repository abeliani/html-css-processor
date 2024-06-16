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

namespace Abeliani\CssJsHtmlOptimizer\Css\Parser\Trait;

use Abeliani\CssJsHtmlOptimizer\Css\Block\RuleEnum;

trait CheckerBlockTrait
{
    private  function isMedia(int $i): bool
    {
        return substr($this->data, $i, 6) === RuleEnum::media->value;
    }

    private function isImport(int $i): bool
    {
        return substr($this->data, $i, 7) === RuleEnum::import->value;
    }

    private function isFontFace(int $i): bool
    {
        return substr($this->data, $i, 10) === RuleEnum::fontface->value;
    }

    private function isKeyFrames(int $i): bool
    {
        return substr($this->data, $i, 10) === RuleEnum::keyframes->value;
    }
}

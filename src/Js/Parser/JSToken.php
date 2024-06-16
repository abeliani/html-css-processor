<?php

namespace Abeliani\CssJsHtmlOptimizer\Js\Parser;

class JSToken
{
    const RL = "\r";
    const NL = "\n";
    const S = ' ';
    const NS = '';
    const QUOTE = "'";
    const D_QUOTE = '"';
    const B_QUOTE = '`';
    const COMMA = ',';
    const EQUAL = '=';
    const B_SLASH = '\\';
    const F_SLASH = '/';
    const SEMICOLON = ';';
    const V = 'v';
    const L = 'l';
    const I = 'i';
    const C = 'c';
    const R = 'r';
    const E = 'e';
    const F = 'f';
    const IF = 'if';
    const VAR = 'var';
    const LET = 'let';
    const CONST = 'const';
    const O_PARENTHESES = '(';
    const O_CUR_BRACKET = '{';
    const C_CUR_BRACKET = '}';
    const C_CUBE_BRACKET = ']';

    const WITH_BRACKET_IF = 'if(';
    const WITH_SPACE_IF = 'if ';
    const WITH_SPACE_ELSE = 'else ';
    const WITH_SPACE_RETURN = 'return ';

    const WITH_BRACKET_FUNCTION = 'function(';
    const WITH_SPACE_FUNCTION = 'function ';

    const PATT_SPACES = '~\s+~';
    const PATT_D_SPACES = '~\s{2,}~';
    const PATT_NON_SPACE = '~\S~';
    const S_PATT_EQUALS = '%s=';
    const S_PATT_CONCAT = '%s%s';
}

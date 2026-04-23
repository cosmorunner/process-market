<?php

namespace App\Graph;

/**
 * Parsed die [[]]-Syntaxen.
 * Class SyntaxParser
 * @package App\Graph
 */
class SyntaxParser {

    const REGEX_ACTION_OUTPUT_TITLE_PATTERN = '/\(\((.*)\)\)/';
    const REGEX_ACTION_OUTPUT = '/^\[\[action\.outputs\.([a-z\d_]+)*]]$/';

    /**
     * Returns the output name for an action output syntax.
     * @param $syntax
     * @return string
     */
    public static function actionOutputName($syntax) {
        // Remove title, if present
        if (preg_match(self::REGEX_ACTION_OUTPUT_TITLE_PATTERN, $syntax, $matches)) {
            $syntax = str_replace($matches[0], '', $syntax);
        }

        // Get the value out of [[action.output.value]] syntax
        preg_match_all(self::REGEX_ACTION_OUTPUT, $syntax, $matches);

        if (empty($matches)) {
            return $syntax;
        }

        return $matches[1][0] ?? $syntax;
    }
}

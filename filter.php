<?php // This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This filter checks spelling and highlight misspelt words.
 *
 * @package    filter_spellcheck
 * @copyright  2017 Daniel Thies (dthies@ccal.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Filter to check spelling.
 */
class filter_spellcheck extends moodle_text_filter {
    /**
     * Stored dictionary to be used for spelling
     */
    public static $dictionary;

    /*
     * This function wraps mispelled words in a span with class to highlight
     *
     * @param string $text The text to filter.
     * @param array $options The filter options.
     */
    public function filter($text, array $options = array()) {
        global $CFG;
        if (empty(filter_spellcheck::$dictionary)) {
            filter_spellcheck::$dictionary = pspell_new("en");
        }
        // Create regular expression to find words to check.
        $rule = '/[A-z]*';
        // Exclude glossary filter links.
        $rule .= '|<a [^<]*class="glossary[^<]*<\\/a>';
        // Do not check tag attributes.
        $rule .= '|<[^>]*>|&[a-z]*;';
        // Ignore TeX expressions.
        $rule .= '|\\$\\$.*?\\$\\$';
        $rule .= '/';
        $text = preg_replace_callback($rule, function($matches) {
            if (!preg_match('/^[A-z]*$/', $matches[0])) {
                return $matches[0];
            }

            if (!pspell_check(filter_spellcheck::$dictionary, $matches[0])) {
                return '<span class="filter_spellcheck" title="check spelling">' . 
                    $matches[0] . '</span>';
            }
            return $matches[0];
        }, $text);
        return $text;
    }

}

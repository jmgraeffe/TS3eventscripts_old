<?php

/*
  The MIT License (MIT)

  Copyright (c) 2014-2015 Karl-Martin Minkner

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
 */

/*
 * EVlang
 * --------------------------------
 * This class loads all languages from the
 * subfolder "lang" into one big array and
 * provides features to read a string for
 * a specific language with fallback to a default
 * language. Otherwise it will return an empty string.
 * 
 * Developers need to check against an empty string!
 */

class EVlang {

    private static $lang = array();
    private static $default = 'en';

    /*
     * Initialize languages
     * --------------------------------
     * + merge languages into one array
     */

    public static function init() {
        $files = scandir('./lang/');
        foreach ($files as $file) {
            if ($file != '.' AND $file != '..') {
                $ex = explode('.', $file);
                if (isset($ex[0])) {
                    include_once('./lang/' . $file);
                    EVmain::log('Language ' . $ex[0] . ' loaded');
                    EVlang::$lang = array_merge($lang, EVlang::$lang);
                }
            }
        }
    }

    /*
     * get string for specific language
     * --------------------------------
     * + fall back for default language
     */

    public static function get($lang, $key) {
        if (isset(EVlang::$lang[$lang][$key])) {
            return EVlang::$lang[$lang][$key];
        } elseif (EVlang::$default != $lang) {
            return EVlang::get(EVlang::$default, $key);
        } else {
            return '';
        }
    }

}

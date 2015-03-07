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
 * EVplugins
 * --------------------------------
 * This class loads all jobs from the
 * jobs folder, initialises the plugins
 * and runs them frequently
 * 
 * Notes for developers: If you create a plugin
 * you have to create a public function called "loop"
 * to get it working with EVplugins. And you should create
 * a constructor to initialize your plugin. Without an job
 * xml file your plugin will not get executed!
 */

class EVplugins {

    // array of all plugins
    private static $plugins = array();

    public static function init() {
        self::load();
    }

    /*
     * load all plugins
     * --------------------------------
     * 
     */

    private static function load() {
        $files = scandir('./jobs/');
        foreach ($files as $file) {
            if ($file != '.' AND $file != '..') {
                // load xml file
                $xml = (array)simplexml_load_file('./jobs/' . $file);
                // load and initialize plugin
                if (isset($xml['plugin'])) {
                    include_once('./plugins/' . $xml['plugin'] . '.php');
                    EVmain::log('Job ' . $file . ' loaded');
                    $plugin = $xml['plugin'];
					// add name to give the plugins the possibility to recognize which object goes to which entry 
                    self::$plugins[$plugin] = new $plugin((array)$xml['options']);
                }else{
                    EVmain::log('Error in xml file '.$file);
                }
            }
        }
    }

	/*
	 * let plugins get the whole list of all plugins
	 * --------------------------------
	 * @name	= name of plugin (without .php)
	 */
	public static function getPlugin($name) {
		if(in_array(self::$plugins)) {
			return self::$plugins[$name];
		}
	}
	
	/*
	 * let plugins get the whole list of all plugins
	 * --------------------------------
	 * 
	 */
	// added this class because $plugins is private
	public static function getPluginList() {
		return $plugins;
	}
    /*
     * loop through all plugins
     * --------------------------------
     * @msg     = array with optional ts3 events
     */

    public static function loop($msg) {
        foreach (self::$plugins as $plugin) {
            $plugin->loop(array('msg' => $msg));
        }
    }

}

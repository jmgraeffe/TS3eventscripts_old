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
 * EVmain
 * --------------------------------
 * This class provides some stuff like
 * logging or parsing. Nothing special
 * but maybe some sweets for you :)
 */

class EVmain {

    // public array with all connected clients if initialized
    public static $clientList = [];
    private static $clientList_interval = 0;
    private static $clientList_lastrun = 0;

    /*
     * log messages for administrator
     * --------------------------------
     * @msg     = message to log
     * [@die]   = should we stop script execution
     */

    public static function log($msg, $die = false) {
        echo date('d.m.Y H:i:s', time()) . ' - ' . $msg . "\n";
        if ($die) {
            exit;
        }
        return $msg;
    }

    /*
     * parse answers from ts3 query
     * --------------------------------
     * @msg     = message from ts3
     */

    public static function parse($msg) {
        $ex0 = explode('|', $msg);
        $tmp = array();
        foreach ($ex0 as $item) {
            $tmp2 = array();
            $ex1 = explode(" ", $item);
            $tmp2['ev_type'] = $ex1[0];
            foreach ($ex1 as $p1) {
                $ex2 = explode("=", $p1, 2);
                if (isset($ex2[1])) {
                    $tmp2[$ex2[0]] = $ex2[1];
                }
            }
            $tmp[] = $tmp2;
        }
        return $tmp;
    }

    /*
     * get clientlist as global array for all plugins
     * --------------------------------
     * @interval    =   set the interval (0 = disabled)
     */

    public static function setClientList($interval) {
        self::$clientList_interval = intval($interval);
    }

    /*
     * loop for getting global values if activated
     * --------------------------------
     * 
     */

    public static function loop() {
        // get client list frequently if activated by a plugin
        if (self::$clientList_interval > 0 && self::$clientList_lastrun <= time() - self::$clientList_interval) {
            // query ts3 to get clientList
            self::$clientList = self::parse(EVts3::clientList('-groups -voice -away -uid -info -country'));
            // set time to now
            self::$clientList_lastrun = time();
        }
    }

}

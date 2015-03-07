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

//error_reporting(0);

include_once('./config.php');

include_once('./classes/database.class.php');
include_once('./classes/socket.class.php');
include_once('./classes/main.class.php');
include_once('./classes/plugin.class.php');
include_once('./classes/lang.class.php');
include_once('./classes/ts3.class.php');

/* init */
EVdb::getInstance(	$config["sql"]["system"], 
					$config["sql"]["host"],
					$config["sql"]["user"],
					$config["sql"]["pass"],
					$config["sql"]["db"]		);
EVsocket::getInstance(	$config["ts3"]["ip"],
						$config["ts3"]["port"],
						$config["ts3"]["timeout"]	);
EVsocket::getInstance()->login(	$config["ts3"]["user"],
								$config["ts3"]["pass"]	);
EVts3::useserver($config["ts3"]["id"]);
EVts3::updatenick($config["name"]);
EVlang::init();
EVplugins::init();

/* main thread */
while (true) {
    $buf = EVsocket::getInstance()->get();
    $ex = explode("\n",$buf);
    foreach($ex as $line) {
        $line = EVts3::unEscapeText($line);
        EVmain::loop($line);
        EVplugins::loop($line);
        usleep(100);
    }
    usleep(100);
}
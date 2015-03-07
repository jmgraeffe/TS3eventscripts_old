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
 * Gameserver Query
 * --------------------------------
 * This plugin queries a gameserver
 * and displays the result in a specific
 * channel.
 * 
 * Easy to use: Channel Name could edited
 * with all variables that the server gives us
 */

/*
 * Version History
 * --------------------------------
 * v0.1
 *      + added proof interval
 *      + added the ability to use
 *        more then 1 channel for info
 */

class kdr_gsquery {

    private $type = '';
    private $ip = '127.0.0.1';
    private $port = '27015';
    private $msgs = array();
    private $timer = 0;
    private $proof_interval = 10;
    private $gameq;
    private $last_sent = '';

    public function __construct($options) {
        // set options
        $this->type = $options['type'];
        $this->ip = $options['ip'];
        $this->port = $options['port'];

        // get all message lines
        $count = 0;
        while (true) {
            if (isset($options['channel_' . $count]) && isset($options['msg_' . $count])) {
                $this->msgs[$options['channel_' . $count]] = $options['msg_' . $count];
            }else{
                break;
            }
            $count++;
        }
        $this->proof_interval = $options['interval'];

        // init class if not loaded
        if (!class_exists('GameQ')) {
            include_once('./libraries/GameQ.php');
        }
        $this->gameq = new GameQ();
    }

    public function loop($array) {
        // if we can execute the next gameserver check
        if ($this->timer <= time() - $this->proof_interval) {
            // query gameserver and display result in specific channel
            $results = $this->query();
            foreach($this->msgs as $chan => $msg) {
                $msg = substr($this->strreplace($results, $msg),0,40);
                if($this->last_sent !== $msg) {
                    $this->last_sent = $msg;
                    $channel = array(
                        'channel_name' => $msg,
                    );
                    EVts3::channelEdit($chan, $channel);
                }
            }
            // set timer to now
            $this->timer = time();
        }
    }

    /*
     * query gameserver
     */

    private function query() {
        $this->gameq->addServer(array(
            'type' => $this->type,
            'host' => $this->ip . ':' . $this->port
        ));
        $this->gameq->setOption('timeout', 4);
        $this->gameq->setFilter('normalise');
        return $this->gameq->requestData()[$this->ip . ':' . $this->port];
    }

    private function strreplace($array, $string) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $string = $this->strreplace($value, $string);
            } else {
                $string = str_replace('{' . $key . '}', $value, $string);
            }
        }
        return $string;
    }

}

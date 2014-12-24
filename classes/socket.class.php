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

class EVsocket {

    private static $socket_events;
    private static $socket;
    private static $instance;

    public function __construct($ip, $port) {
        self::connect($ip, $port);
    }

    public static function getInstance($ip = '127.0.0.1', $port = 10011) {
        if (is_null(self::$instance)) {
            self::$instance = new self($ip, $port);
        }
        return self::$instance;
    }

    private static function connect($ip, $port) {
        $address = "tcp://" . $ip . ":" . $port;
        $timeout = 1;
        self::$socket_events = @stream_socket_client($address, $errno, $errstr, $timeout);
        self::$socket = @stream_socket_client($address, $errno, $errstr, $timeout);
        if (self::$socket === false || self::$socket_events === false) {
            EVmain::log('Could not connect to teamspeak3 server query', true);
        }
        @stream_set_timeout(self::$socket_events, $timeout);
        @stream_set_timeout(self::$socket, $timeout);
        @stream_set_blocking(self::$socket_events, 0);
        @stream_set_blocking(self::$socket, 1);
        EVmain::log('connected to teamspeak3 server query');
    }

    public static function login($user, $pass) {
        self::send('login ' . $user . ' ' . $pass);
        self::send_events('login ' . $user . ' ' . $pass);
    }

    public static function send_events($msg) {
        stream_socket_sendto(self::$socket_events, $msg . "\n");
    }

    public static function send($msg) {
        stream_socket_sendto(self::$socket, $msg . "\n");
        return trim(@stream_get_contents(self::$socket, 4096));
    }
    
    public static function get_events() {
        return trim(@stream_get_contents(self::$socket_events, 4096));
    }

    public static function get() {
        return trim(@stream_get_contents(self::$socket, 4096));
    }

    public static function close() {
        fclose(self::$socket);
    }

}

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
 * EVsocket
 * --------------------------------
 * This class creates the connection between teamspeak
 * and TS3eventscripts. It provides two sockets, one for
 * all event stuff (non blocking) and another (blocking) 
 * for all other stuff like poking, messaging, ...
 * 
 * Please not that you maybe need to whitelist your IP
 * from the TS3eventscripts server or otherwise you
 * will get ip banned because of flooding.
 */

class EVsocket {

    private static $socket_blocked;
    private static $socket;
    private static $instance;
    private static $maxlength = -1;

    public function __construct($ip, $port, $timeout) {
        self::connect($ip, $port, $timeout);
    }

    /*
     * create / get instance of socket class
     * --------------------------------
     * @ip      = ip of teamspeak server query
     * @port    = port of teamspeak server query
     */

    public static function getInstance($ip = '127.0.0.1', $port = 10011, $timeout = 0.5) {
        if (is_null(self::$instance)) {
            self::$instance = new self($ip, $port, $timeout);
        }
        return self::$instance;
    }

    /*
     * connection to teamspeak server query
     * --------------------------------
     * @ip      = ip of teamspeak server query
     * @port    = port of teamspeak server query
     */

    private static function connect($ip, $port, $timeout) {
        $address = "tcp://" . $ip . ":" . $port;
        self::$socket_blocked = @stream_socket_client($address, $errno, $errstr, $timeout);
        self::$socket = @stream_socket_client($address, $errno, $errstr, $timeout);
        if (self::$socket === false || self::$socket_blocked === false) {
            EVmain::log('Could not connect to teamspeak3 server query', true);
        }
        @stream_set_timeout(self::$socket_blocked, $timeout);
        @stream_set_timeout(self::$socket, $timeout);
        @stream_set_blocking(self::$socket_blocked, 1);
        @stream_set_blocking(self::$socket, 0);
        EVmain::log('connected to teamspeak3 server query');
    }

    /*
     * login as server query admin
     * --------------------------------
     * @user    = username of serverquery
     * @pass    = password of serverquery
     */

    public static function login($user, $pass) {
        self::send('login ' . $user . ' ' . $pass);
        self::send_blocking('login ' . $user . ' ' . $pass);
    }

    /*
     * send message to events socket
     * --------------------------------
     * @msg     = message
     */

    public static function send_blocking($msg) {
        stream_socket_sendto(self::$socket_blocked, $msg . "\n");
        EVmain::log($msg);
        return trim(@stream_get_contents(self::$socket_blocked, self::$maxlength));
    }

    /*
     * send message to socket
     * --------------------------------
     * @msg     = message
     */

    public static function send($msg) {
        return stream_socket_sendto(self::$socket, $msg . "\n");
    }

    /*
     * get answer from socket
     * --------------------------------
     * 
     */

    public static function get() {
        return trim(@stream_get_contents(self::$socket, self::$maxlength));
    }

    /*
     * close socket connection
     * --------------------------------
     * 
     */

    public static function close() {
        fclose(self::$socket);
        //fclose(self::$socket_events);
    }

}

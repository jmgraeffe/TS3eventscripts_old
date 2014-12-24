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

class EVdb {

    private static $type;
    private static $socket;
    private static $instance;

    public function __construct($type, $host, $user, $pass, $db) {
        self::$type = $type;
        self::connect($host, $user, $pass, $db);
    }

    public static function getInstance($type = 'mysqli', $host = '127.0.0.1', $user = 'root', $pass = '', $db = 'ts3eventscripts') {
        if (is_null(self::$instance)) {
            self::$instance = new self($type, $host, $user, $pass, $db);
        }
        return self::$instance;
    }

    private static function connect($host, $user, $pass, $db) {
        switch (self::$type) {
            case 'mysqli':
                self::$socket = new mysqli($host, $user, $pass, $db);
                (self::$socket->connect_errno) ? EVmain::log('SQL-Connection failed ' . self::$socket->connect_errno . ' (mysqli)', true) : '';
                break;
            case 'sqlite3':
                self::$socket = new SQLite3($db);
                break;
        }
    }

    public static function query($query) {
        switch (self::$type) {
            case 'mysqli':
                return self::$socket->query($query);
                break;
            case 'sqlite3':
                return self::$socket->exec($query);
                break;
        }
    }
    
    public static function escapeText($text) {
        switch (self::$type) {
            case 'mysqli':
                return self::$socket->escape_string ($text);
                break;
            case 'sqlite3':
                return self::$socket->escapeString($text);
                break;
        }
    }

    public static function numrows($res) {
        switch (self::$type) {
            case 'mysqli':
                return $res->num_rows;
                break;
        }
    }

    public static function fetch_assoc($res) {
        switch (self::$type) {
            case 'mysqli':
                return $res->fetch_assoc();
                break;
        }
    }

    public static function fetch_array($res) {
        switch (self::$type) {
            case 'mysqli':
                return $res->fetch_array();
                break;
            case 'mysqli':
                return $res->fetchArray();
                break;
        }
    }

    public static function fetch_field($res) {
        switch (self::$type) {
            case 'mysqli':
                return $res->fetch_field();
                break;
        }
    }

    public static function lastID() {
        switch (self::$type) {
            case 'mysqli':
                return self::$socket->insert_id();
                break;
            case 'sqlite3':
                return self::$socket->lastInsertRowid();
                break;
        }
    }

}

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

class EVts3 {
    /* helper functions */

    public static function unEscapeText($text) {
        $escapedChars = array("\t", "\v", "\r", "\n", "\f", "\s", "\p", "\/");
        $unEscapedChars = array('', '', '', '', '', ' ', '|', '/');
        $text = str_replace($escapedChars, $unEscapedChars, $text);
        return $text;
    }

    public static function escapeText($text) {
        $text = str_replace("\t", '\t', $text);
        $text = str_replace("\v", '\v', $text);
        $text = str_replace("\r", '\r', $text);
        $text = str_replace("\n", '\n', $text);
        $text = str_replace("\f", '\f', $text);
        $text = str_replace(' ', '\s', $text);
        $text = str_replace('|', '\p', $text);
        $text = str_replace('/', '\/', $text);
        return $text;
    }

    /* teamspeak commands */

    public static function ev_regevent($type, $id = '') {
        $tmp = 'servernotifyregister event=' . $type;
        if (!empty($id)) {
            $tmp.= ' id=' . $id;
        }
        EVsocket::send_events($tmp);
    }

    public static function ev_useserver($id) {
        $tmp = 'use sid=' . $id;
        EVsocket::send($tmp);
        EVsocket::send_events($tmp);
    }

    public static function ev_updatenick($name) {
        $tmp = 'clientupdate client_nickname=' . self::escapeText($name);
        EVsocket::send($tmp);
        EVsocket::send_events($tmp);
    }

    public static function ev_sendmessage($targetmode, $target, $msg) {
        $msg = str_replace(' ', '\s', $msg);
        $tmp = 'sendtextmessage targetmode=' . $targetmode . ' target=' . $target . ' msg=' . self::escapeText($msg);
        EVsocket::send($tmp);
    }

    public static function ev_clientList($params = null) {
        if (!empty($params)) {
            $params = ' ' . $params;
        }
        $tmp = 'clientlist' . $params;
        return EVsocket::send($tmp, true);
    }
    
    public static function ev_clientMove($clid,$cid,$cpw = '') {
        $tmp = 'clientmove clid='.$clid.' cid='.$cid.(!empty($cpw) ? ' cpw='.self::escapeText($cpw) : '');
        return EVsocket::send($tmp, true);
    }

}

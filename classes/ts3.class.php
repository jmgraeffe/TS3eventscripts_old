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
 * EVts3
 * --------------------------------
 * This class contains all the stuff for
 * teamspeak server communication. It's not
 * finished yet but will get updated from time
 * to time. Feel free to contribute your changes.
 * 
 * Developers need to check the "answer" of the ts3server
 * manually.
 */

class EVts3 {
    /* helper functions */

    /*
     * unescape text that we have got from teamspeak
     * --------------------------------
     * thanks to ts3admin.info :)
     */

    public static function unEscapeText($text) {
        $escapedChars = array("\t", "\v", "\r", "\n", "\f", "\s", "\p", "\/");
        $unEscapedChars = array('', '', '', '', '', ' ', '|', '/');
        $text = str_replace($escapedChars, $unEscapedChars, $text);
        return $text;
    }

    /*
     * escape text that we send to teamspeak
     * --------------------------------
     * thanks to ts3admin.info :)
     */

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

    /*
     * register an event for teamspeak
     * --------------------------------
     * @type    = type of event
     * [@id]    = id of event
     */

    public static function regevent($type, $id = '') {
        $tmp = 'servernotifyregister event=' . $type;
        if (!empty($id)) {
            $tmp.= ' id=' . $id;
        }
        EVsocket::send_events($tmp);
    }

    /*
     * sets the active virtual ts3 server
     * --------------------------------
     * @id      = id of virtual server
     */

    public static function useserver($id) {
        $tmp = 'use sid=' . $id;
        EVsocket::send($tmp);
        EVsocket::send_events($tmp);
    }

    /*
     * set the nickname of server query
     * --------------------------------
     * @name    = nickname of server query
     */

    public static function updatenick($name) {
        $tmp = 'clientupdate client_nickname=' . self::escapeText($name);
        EVsocket::send($tmp);
        EVsocket::send_events($tmp);
    }

    /*
     * send a message to a specific target
     * --------------------------------
     * @targetmode  = mode of message
     * @target      = target
     * @msg         = message
     */

    public static function sendmessage($targetmode, $target, $msg) {
        $msg = str_replace(' ', '\s', $msg);
        $tmp = 'sendtextmessage targetmode=' . $targetmode . ' target=' . $target . ' msg=' . self::escapeText($msg);
        EVsocket::send($tmp);
    }

    /*
     * get the list of connected clients
     * --------------------------------
     * [@params]    =   get userlist with some parameters
     */

    public static function clientList($params = null) {
        if (!empty($params)) {
            $params = ' ' . $params;
        }
        $tmp = 'clientlist' . $params;
        return EVsocket::send($tmp, true);
    }

    /*
     * move client to other channel
     * --------------------------------
     * @clid    = user id of client
     * @cid     = channel id
     * [@cpw]   = password for channel
     */

    public static function clientMove($clid, $cid, $cpw = '') {
        $tmp = 'clientmove clid=' . $clid . ' cid=' . $cid . (!empty($cpw) ? ' cpw=' . self::escapeText($cpw) : '');
        return EVsocket::send($tmp, true);
    }

    /*
     * poke specific client
     * --------------------------------
     * @clid    = user id of client
     * @msg     = message for poke
     */

    public static function clientPoke($clid, $msg) {
        $tmp = 'clientpoke clid=' . $clid . ' msg=' . self::escapeText($msg);
        return EVsocket::send($tmp, true);
    }

    /*
     * edit specific channel
     * --------------------------------
     * @cid    = id of channel
     * @data     = options to change
     */
    public static function channelEdit($cid, $data) {
        $params = '';
        foreach ($data as $key => $value) {
            $params.= ' ' . $key . '=' . self::escapeText($value);
        }
        $tmp = 'channeledit cid=' . $cid . $params;
        return EVsocket::send($tmp, true);
    }

}

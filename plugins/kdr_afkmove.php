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
 * AFKmove
 * --------------------------------
 * This plugin moves everybody with
 * muted headphones to an specific
 * channel and back after unmute.
 * 
 * easy to use - just specify the
 * afk room ID and proof interval.
 * Do not set it below 3! Otherwise
 * it could affect other plugins on
 * full servers.
 */

/*
 * Version History
 * --------------------------------
 * v0.1
 *      + added proof interval
 *      + 
 */

class kdr_afkmove {

    private $afk_room_id = 0;
    private $proof_interval = 10;
    private $afklist = array();
    private $timer = 0;

    public function __construct($options) {
        // set options
        $this->afk_room_id = $options['afkroom'];
        $this->proof_interval = $options['interval'];
        // register event and listen for joining / leaving people
        EVts3::regevent('server');
        // register global clientList
        EVmain::setClientList($this->proof_interval);
    }

    public function loop($array) {
        // remove client from afklist if he disconnects
        if (!empty($array['msg'])) {
            $array = EVmain::parse($array['msg']);
            if (isset($array['type']) && $array['type'] == 'notifyclientleftview') {
                unset($this->afklist[$array['clid']]);
            }
        }
        // if we can execute the next proof for clients
        if ($this->timer <= time() - $this->proof_interval) {
            // get clientList and execute function proof_move
            $this->proof_move(EVmain::$clientList);
            // set timer to now
            $this->timer = time();
        }
    }

    /*
     * if there is someone to move in AFK room
     */

    private function proof_move($array) {
        //print_r($array);
        foreach ($array as $user) {
            // only switch real users and if client id exists
            if (isset($user['client_type']) && $user['client_type'] == 0 && isset($user['clid'])) {
                // if headphones muted execute movafk otherwise moveback
                if ($user['client_output_muted'] == 1) {
                    $this->moveAfk($user);
                } else {
                    $this->moveBack($user);
                }
            }
        }
    }

    private function moveAfk($user) {
        // if the user isn't already afk
        if (!isset($this->afklist[$user['clid']])) {
            // move him to afk room
            EVts3::clientMove($user['clid'], $this->afk_room_id);
            // add him to the afklist
            $this->afklist[$user['clid']] = $user['cid'];
        }
    }

    private function moveBack($user) {
        // if the user was AFK and isn't any more
        if (isset($this->afklist[$user['clid']])) {
            // move back to old channel
            EVts3::clientMove($user['clid'], $this->afklist[$user['clid']]);
            // remove from list
            unset($this->afklist[$user['clid']]);
        }
    }

}

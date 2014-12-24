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

class kdr_afkmove {

    private $clientlist = array();
    private $afk_room_id = 2940;
    private $afklist = array();

    public function __construct() {
        // listen to disconnecting user
        EVts3::ev_regevent('server');
        // set timer
        $this->timer['clientlist'] = 0;
    }

    public function loop($array) {
        // if a client disconnects we have to clear the array entry
        if(!empty($array['msg'])) {
            $array = EVmain::parse($array['msg']);
            if(isset($array['ev_type']) && $array['ev_type'] == 'notifyclientleftview') {
                unset($this->afklist[$array['clid']]);
            }
        }
        // timer
        foreach ($this->timer as $key => $time) {
            if ($time <= time() - 5) {
                $this->timer[$key] = time();
                switch ($key) {
                    case 'clientlist':
                        $this->proof_move(EVmain::parse(EVts3::ev_clientList('-groups -voice -away -uid -info -country')));
                        break;
                }
            }
        }
    }

    /*
     * if there is someone to move in AFK room
     */

    private function proof_move($array) {
        //print_r($array);
        foreach ($array as $user) {
            // only switch real users
            if ($user['client_type'] == 0) {
                if ($user['client_output_muted'] == 1) {
                    $this->moveAfk($user);
                }else{
                    $this->moveBack($user);
                }
            }
        }
    }
    
    private function moveAfk($user) {
        if(!isset($this->afklist[$user['clid']])) {
            EVts3::ev_clientMove($user['clid'],$this->afk_room_id);
            $this->afklist[$user['clid']] = $user['cid'];
        }
    }
    
    private function moveBack($user) {
        if(isset($this->afklist[$user['clid']])) {
            EVts3::ev_clientMove($user['clid'],$this->afklist[$user['clid']]);
            unset($this->afklist[$user['clid']]);
        }
    }
}

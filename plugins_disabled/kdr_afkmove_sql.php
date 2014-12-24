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

    public function __construct() {
        EVdb::query("CREATE TABLE IF NOT EXISTS `kdr_afkmove` ("
                . "`id` int(11) NOT NULL AUTO_INCREMENT,"
                . "`user_id` int(11) NOT NULL,"
                . "`user_uid` varchar(100) COLLATE utf8_bin NOT NULL,"
                . "`last_channel` int(11) NOT NULL,"
                . "`timestamp` int(11) NOT NULL,"
                . "primary key (id))"
                . " ENGINE=InnoDB DEFAULT"
                . " CHARSET=utf8"
                . " COLLATE=utf8_bin"
                . " AUTO_INCREMENT=1");
        EVdb::query("TRUNCATE kdr_afkmove");
        // set timer
        $this->timer['clientlist'] = 0;
    }

    public function loop($array) {
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
        //print_r($user);
        $query = EVdb::query("SELECT * FROM kdr_afkmove WHERE user_uid = '".EVdb::escapeText($user['client_unique_identifier'])."' AND user_id='".intval($user['clid'])."' LIMIT 0,1");
        if(EVdb::numrows($query) == 0) {
            print_r(EVts3::ev_clientMove($user['clid'],$this->afk_room_id));
            EVdb::query("INSERT INTO kdr_afkmove (user_id,user_uid,last_channel,timestamp)VALUES('".intval($user['clid'])."','".EVdb::escapeText($user['client_unique_identifier'])."','".intval($user['cid'])."','".time()."')");
        }
    }
    
    private function moveBack($user) {
        $query = EVdb::query("SELECT * FROM kdr_afkmove WHERE user_uid = '".EVdb::escapeText($user['client_unique_identifier'])."' AND user_id='".intval($user['clid'])."' LIMIT 0,1");
        if(EVdb::numrows($query) > 0) {
            $row = EVdb::fetch_array($query);
            EVts3::ev_clientMove($row['user_id'],$row['last_channel']);
            EVdb::query("DELETE FROM kdr_afkmove WHERE id = '".intval($row['id'])."'");
        }
    }
}

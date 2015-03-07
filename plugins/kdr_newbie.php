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
 * Newbie Query
 * --------------------------------
 * This Plugin says "hello" to a newbie.
 * A newbie can chat with the server.
 */

/*
 * Version History
 * --------------------------------
 * v0.1
 *      + write message to specific user group
 */

class kdr_newbie {
    private $guestgroupid = 8;

    public function __construct($options) {
        $this->guestgroupid == $options['guestgroupid'];
        EVts3::regevent('server');
        EVts3::regevent('textprivate');
        EVts3::regevent('textchannel');
    }

    public function loop($array) {
        // client connected?
        if (!empty($array['msg'])) {
            $array = EVmain::parse($array['msg'])[0];
            // user connected
            if (isset($array['ev_type']) && $array['ev_type'] == 'notifycliententerview') {
                // user got guest group
                if($array['client_servergroups'] == $this->guestgroupid) {
                    $evlang = EVlang::get('de','newbie_connected');
                    foreach($array as $key=>$val) {
                        $evlang = str_replace('{'.$key.'}',$val,$evlang);
                    }
                    EVts3::sendmessage(1,$array['clid'],$evlang);
                }
                //[ev_type] => notifycliententerview
                //[cfid] => 0
                //[ctid] => 1
                //[reasonid] => 0
                //[clid] => 7
                //[client_unique_identifier] => jiwefeifks....
                //[client_nickname] => Kalle
                //[client_input_muted] => 0
                //[client_output_muted] => 0
                //[client_outputonly_muted] => 0
                //[client_input_hardware] => 1
                //[client_output_hardware] => 1
                //[client_is_recording] => 0
                //[client_database_id] => 2
                //[client_channel_group_id] => 8
                //[client_servergroups] => 6
                //[client_away] => 0
                //[client_type] => 0
                //[client_talk_power] => 75
                //[client_talk_request] => 0
                //[client_is_talker] => 0
                //[client_is_priority_speaker] => 0
                //[client_unread_messages] => 0
                //[client_needed_serverquery_view_power] => 75
                //[client_icon_id] => 0
                //[client_is_channel_commander] => 0
                //[client_channel_group_inherited_channel_id] => 1
            }else if (isset($array['ev_type']) && $array['ev_type'] == 'notifytextmessage') {
                // if we got a text message from a user
                
                //[ev_type] => notifytextmessage
                //[targetmode] => 1
                //[msg] => test
                //[target] => 4
                //[invokerid] => 7
                //[invokername] => Kalle1
                //[invokeruid] => XEK/udHR4GCbPVBfnbe/6FJCnxY=
                
                $ex = explode(' ',$array['msg']);
                if(isset($ex[0]) && $ex[0] == '!test') {
                    echo 'test';
                }
            }
        }
    }
}

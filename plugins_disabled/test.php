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

class test {

    private $lang = array();
    private $clients = array();
    private $timer = array();
    private $clientlist = array();

    public function __construct() {
        // construct if needed
        EVts3::ev_regevent('server');
        EVts3::ev_regevent('textprivate');

        // language
        $this->lang['en'] = array(
            'msg_welcome' => '\nWelcome on our Teamspeak!\n'
            . 'If you are new please wait for an Admin that moves you.\n'
            . 'Otherwise we wish you a nice stay!\n'
            . '\n'
            . 'Server commands: (write into this chat)\n'
            . 'Change language:\n'
            . '- !de (GERMAN)\n'
            . '- !en (ENGLISH)\n',
            'lang_changed' => 'Changed language to english :)',
            'pong' => 'Pong!',
        );
        $this->lang['de'] = array(
            'msg_welcome' => '\nWillkommen auf unserem Teamspeak!\n'
            . 'Falls du neu bist warte bitte auf einen Admin. Du benötigst eine Servergruppe um mit deinen Freunden spielen zu können.'
            . '\nWir wünschen dir einen angenehmen Aufenthalt!\n'
            . '\n'
            . 'Serverbefehle: (in den Chat schreiben)\n'
            . 'Sprache ändern:\n'
            . '- !de (Deutsch)\n'
            . '- !en (Englisch)\n',
            'lang_changed' => 'Sprache zu deutsch gewechselt :)',
            'pong' => 'Ping zurück :P',
        );

        // timer
        $this->timer['clientlist'] = 0;
    }

    public function loop($array) {
        $msg = $array['msg'];
        if (!empty($msg)) {
            EVmain::log($msg);
            $this->types(EVmain::parse($msg));
        }
        // timer
        foreach ($this->timer as $key => $time) {
            if ($time <= time() - 5) {
                $this->timer[$key] = time();
                switch ($key) {
                    case 'clientlist':
                        //print_r(EVmain::parse(EVts3::ev_clientList('-groups -voice -away -uid -info -country')));
                        break;
                }
            }
        }
    }

    public function types($msgs) {
        foreach ($msgs as $msg) {
            print_r($msg);
            switch ($msg['ev_type']) {
                case 'notifycliententerview':
                    $this->user_connected($msg);
                    break;
                case 'notifytextmessage':
                    $this->user_message($msg);
                    break;
            }
        }
    }

    public function lang($uid, $key) {
        if (!isset($this->clients[$uid]) OR ! isset($this->lang[$this->clients[$uid]['lang']])) {
            return $this->lang['en'][$key];
        } else {
            return $this->lang[$this->clients[$uid]['lang']][$key];
        }
    }

    public function msg($id, $uid, $msg) {
        EVts3::ev_sendmessage(1, $id, $this->lang($uid, $msg));
    }

    /* registered events */

    private function user_connected($msg) {
        $this->msg($msg['clid'], $msg['client_unique_identifier'], 'msg_welcome');
    }

    private function user_message($msg) {
        switch ($msg['msg']) {
            case '!en':
                $this->clients[$msg['invokeruid']]['lang'] = 'en';
                $this->msg($msg['invokerid'], $msg['invokeruid'], 'lang_changed');
                break;
            case '!de':
                $this->clients[$msg['invokeruid']]['lang'] = 'de';
                $this->msg($msg['invokerid'], $msg['invokeruid'], 'lang_changed');
                break;
            case '!pong':
                $this->msg($msg['invokerid'], $msg['invokeruid'], 'pong');
                break;
        }
    }

}

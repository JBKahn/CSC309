<?php

class Board extends CI_Controller
{
     
    function __construct()
    {
        // Call the Controller constructor
        parent::__construct();
        session_start();
    }
          
    public function _remap($method, $params = array())
    {
        // enforce access control to protected functions
        if (!isset($_SESSION['user'])) {
            redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
        }
         
        return call_user_func_array(array($this, $method), $params);
    }
    
    
    public function index()
    {
        $user = $_SESSION['user'];
        $this->load->model('user_model');
        $this->load->model('invite_model');
        $this->load->model('match_model');
        
        $user = $this->user_model->get($user->login);

        $invite = $this->invite_model->get($user->invite_id);

        if ($user->user_status_id == User::WAITING) {
            $invite = $this->invite_model->get($user->invite_id);
            $otherUser = $this->user_model->getFromId($invite->user2_id);
        } else {
            if ($user->user_status_id == User::PLAYING) {
                $match = $this->match_model->get($user->match_id);
                if ($match->user1_id == $user->id) {
                    $otherUser = $this->user_model->getFromId($match->user2_id);
                } else {
                    $otherUser = $this->user_model->getFromId($match->user1_id);
                }
            }
        }

        if ($invite->user1_id==$user->id) {
            $data['playerNum'] = 2;
        } else {
            $data['playerNum'] = 1;
        }

        $data['user']=$user;
        $data['otherUser']=$otherUser;
        
        switch($user->user_status_id) {
            case User::PLAYING:
                $data['status'] = 'playing';
                break;
            case User::WAITING:
                $data['status'] = 'waiting';
                break;
        }
            
        $this->load->view('match/board', $data);
    }

    public function getMatchState($board_state)
    {
        for ($row = 0; $row < 3; $row++) {
            for ($column = 0; $column < 6; $column++) {
                $values = array($board_state[(($row) * 7) + $column], $board_state[(($row + 1) * 7) + $column], $board_state[(($row + 2) * 7) + $column], $board_state[(($row + 3) * 7) + $column]);
                if ((count(array_unique($values)) === 1) && $board_state[(($row) * 7) + $column] !== 0) {
                    return $board_state[(($row) * 7) + $column] + 1;
                }
            }
        }

        // Check for horizontal wins
        for ($row = 0; $row < 6; $row++) {
            for ($column = 0; $column < 4; $column++) {
                $values = array($board_state[($row * 7) + $column], $board_state[($row * 7) + $column + 1], $board_state[($row * 7) + $column + 2], $board_state[($row * 7) + $column + 3]);
                if ((count(array_unique($values)) === 1) && $board_state[(($row) * 7) + $column] !== 0) {
                    return $board_state[(($row) * 7) + $column] + 1;
                }
            }
        }

        // Check for diagonal
        for ($row = 0; $row < 3; $row++) {
            for ($column = 0; $column < 4; $column++) {
                $values = array($board_state[($row * 7) + $column], $board_state[(($row + 1) * 7) + $column + 1], $board_state[(($row + 2) * 7) + $column + 2], $board_state[(($row + 3) * 7) + $column + 3]);
                if ((count(array_unique($values)) === 1) && $board_state[(($row) * 7) + $column] !== 0) {
                    return $board_state[(($row) * 7) + $column] + 1;
                }
            }
            for ($column = 3; $column < 7; $column++) {
                $values = array($board_state[($row * 7) + $column], $board_state[(($row + 1) * 7) + $column - 1], $board_state[(($row + 2) * 7) + $column - 2], $board_state[(($row + 3) * 7) + $column - 3]);
                if ((count(array_unique($values)) === 1) && $board_state[(($row) * 7) + $column] !== 0) {
                    return $board_state[(($row) * 7) + $column] + 1;
                }
            }
        }
        $board_values = array_slice($board_state, 0, -1);
        $unqiue_board_values = array_unique($board_values);
        if (count($unqiue_board_values) === 2 && in_array(1, $unqiue_board_values) && in_array(2, $unqiue_board_values)) {
            return 4;
        }
        return 1;
    }

    public function getGameState()
    {
        $this->load->model('user_model');
        $this->load->model('match_model');
        $user = $_SESSION['user'];
        $user = $this->user_model->getExclusive($user->login);
        if ($user->user_status_id != User::PLAYING) {
            $errormsg = "The User State is not PLAYING";
            goto error;
        }

        $match = $this->match_model->get($user->match_id);
        $board_state = unserialize($match->board_state);
        $match_status = $this->getMatchState($board_state);

        if ($match_status !== 1) {
            $this->match_model->updateMatchStatus($match->id, $match_status);
            $this->match_model->updateState($match->id, $board_state);
        }

        echo json_encode(array('status'=>'success','match_status'=>json_encode($match_status),'board_state'=>json_encode(unserialize($match->board_state))));
        return;

        error:
            echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }

    public function findFirstEmptySlotInColumn($column, $board_state)
    {
        for ($row = 5; $row >= 0; $row--) {
            if ($board_state[$row * 7 + $column] == 0) {
                return $row * 7 + $column;
            }
        }
        return -1;
    }

    public function postGameState()
    {
        $this->load->model('user_model');
        $this->load->model('match_model');
        $user = $_SESSION['user'];
        $user = $this->user_model->getExclusive($user->login);
        if ($user->user_status_id != User::PLAYING) {
            $errormsg="The User State is not PLAYING";
            goto error;
        }

        $match = $this->match_model->get($user->match_id);
        if ($match->match_status_id!=1) {
            $errormsg = "Game has already ended.";
            goto error;
        }

        $board_state = unserialize($match->board_state);
        $wrongUserMoved = (($match->user1_id == $user->id) && $board_state[42] == 2) || (($match->user2_id == $user->id) && $board_state[42] == 1);
        if ($wrongUserMoved) {
            $errormsg = "It is not your turn.";
            goto error;
        }

        $column_clicked = (int)$this->input->post('column_clicked');
        $indexForChecker = $this->findFirstEmptySlotInColumn($column_clicked, $board_state);

        if ($indexForChecker === -1) {
            $errormsg = "This column is full";
            goto error;
        }
        if ($match->user1_id == $user->id) {
            $board_state[$indexForChecker] = 1;
            $board_state[42] = 2;
        } else {
            $board_state[$indexForChecker] = 2;
            $board_state[42] = 1;
        }

        $this->match_model->updateState($match->id, $board_state);
        echo json_encode(array('status'=>'success'));
        return;

        error:
            echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }

    public function postMsg()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('msg', 'Message', 'required');
         
        if ($this->form_validation->run() == true) {
            $this->load->model('user_model');
            $this->load->model('match_model');

            $user = $_SESSION['user'];

            $user = $this->user_model->getExclusive($user->login);
            if ($user->user_status_id != User::PLAYING) {
                $errormsg="Not in PLAYING state";
                goto error;
            }
             
            $match = $this->match_model->get($user->match_id);
             
            $msg = $this->input->post('msg');
             
            if ($match->user1_id == $user->id) {
                $msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
                $this->match_model->updateMsgU1($match->id, $msg);
            } else {
                $msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
                $this->match_model->updateMsgU2($match->id, $msg);
            }
                 
            echo json_encode(array('status'=>'success'));
              
            return;
        }
        $errormsg="Missing argument";
        error:
            echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }
 
    public function getMsg()
    {
        $this->load->model('user_model');
        $this->load->model('match_model');
             
        $user = $_SESSION['user'];
          
        $user = $this->user_model->get($user->login);
        if ($user->user_status_id != User::PLAYING) {
            $errormsg="Not in PLAYING state";
            goto error;
        }
        // start transactional mode
        $this->db->trans_begin();
             
        $match = $this->match_model->getExclusive($user->match_id);
             
        if ($match->user1_id == $user->id) {
            $msg = $match->u2_msg;
            $this->match_model->updateMsgU2($match->id, "");
        } else {
            $msg = $match->u1_msg;
            $this->match_model->updateMsgU1($match->id, "");
        }

        if ($this->db->trans_status() === false) {
            $errormsg = "Transaction error";
            goto transactionerror;
        }
         
        // if all went well commit changes
        $this->db->trans_commit();
         
        echo json_encode(array('status'=>'success','message'=>$msg));
        return;
        
        transactionerror:
        $this->db->trans_rollback();
        
        error:
        echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }
}

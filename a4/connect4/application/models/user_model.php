<?php
class User_model extends CI_Model
{
    
    public function get($username)
    {
        $this->db->where('login', $username);
        $query = $this->db->get('user');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'User');
        } else {
            return null;
        }
    }
    
    public function getFromId($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('user');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'User');
        } else {
            return null;
        }
    }
    
    public function getFromEmail($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('user');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'User');
        } else {
            return null;
        }
    }
    
    public function insert($user)
    {
        return $this->db->insert('user', $user);
    }
    
    public function updatePassword($user)
    {
        $this->db->where('id', $user->id);
        return $this->db->update(
            'user',
            array(
                'password'=>$user->password,
                'salt' => $user->salt
            )
        );
    }
    
    public function updateStatus($id, $status)
    {
        $this->db->where('id', $id);
        return $this->db->update('user', array('user_status_id'=>$status));
    }
    
    public function updateInvitation($id, $invitationId)
    {
        $this->db->where('id', $id);
        return $this->db->update('user', array('invite_id'=>$invitationId));
    }
    
    public function updateMatch($id, $matchId)
    {
        $this->db->where('id', $id);
        return $this->db->update('user', array('match_id'=>$matchId));
    }
    
    
    public function getAvailableUsers()
    {
        $this->db->where('user_status_id', User::AVAILABLE);
        $query = $this->db->get('user');
        if ($query && $query->num_rows() > 0) {
            return $query->result('User');
        } else {
            return null;
        }
    }
    
    public function getExclusive($username)
    {
        $sql = "select * from user where login=? for update";
        $query = $this->db->query($sql, array($username));
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'User');
        } else {
            return null;
        }
    }
}

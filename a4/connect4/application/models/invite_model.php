<?php
class Invite_model extends CI_Model
{
    
    public function get($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('invite');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'Invite');
        } else {
            return null;
        }
    }

    public function getUser1($userId)
    {
        $this->db->where('user1_id', $userId);
        $query = $this->db->get('invite');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'Invite');
        } else {
            return null;
        }
    }
    
    public function getUser2($userId)
    {
        $this->db->where('user2_id', $userId);
        $query = $this->db->get('invite');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'Invite');
        } else {
            return null;
        }
    }
    
    
    public function insert($invite)
    {
        return $this->db->insert('invite', $invite);
    }
    
    
    public function updateStatus($id, $status)
    {
        $this->db->where('id', $id);
        return $this->db->update('invite', array('invite_status_id'=>$status));
    }
}

<?php
class Match_model extends CI_Model {
    
    public function getExclusive($id)
    {
        $sql = "select * from `match` where id=? for update";
        $query = $this->db->query($sql, array($id));
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'Match');
        } else {
            return null;
        }
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('match');
        if ($query && $query->num_rows() > 0) {
            return $query->row(0, 'Match');
        } else {
            return null;
        }
    }
    
    
    public function insert($match)
    {
        return $this->db->insert('match', $match);
    }
    
    
    public function updateMsgU1($id, $msg)
    {
        $this->db->where('id', $id);
        return $this->db->update('match', array('u1_msg'=>$msg));
    }
    
    public function updateMsgU2($id, $msg)
    {
        $this->db->where('id', $id);
        return $this->db->update('match', array('u2_msg'=>$msg));
    }

    public function updateState($id, $state)
    {
        $this->db->where('id', $id);
        return $this->db->update('match', array('board_state'=>serialize($state)));
    }

    public function updateMatchStatus($id, $status)
    {
        $this->db->where('id', $id);
        return $this->db->update('match', array('match_status_id'=>$status));
    }
}

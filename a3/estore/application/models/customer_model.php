<?php
class Customer_model extends CI_Model {

    function getAll() {
        $query = $this->db->get('customers');
        return $query->result('Customer');
    }

    function get($id) {
        $query = $this->db->get_where('customers',array('id' => $id));
        return $query->row(0,'Customer');
    }

    function delete($id) {
        return $this->db->delete("customers",array('id' => $id ));
    }

    function insert($input) {
        return $this->db->insert(
            "customers",
            array(
                'first' => $input['first'],
                'last' => $input['last'],
                'login' => $input['login'],
                'password' => $input['password'],
                'email' => $input['email']
            )
        );
    }

    function isValidLogin($customer) {
        $query = $this->db->get_where(
            'customers',
            array(
                'login'=>$customer->login,
                'password'=>$customer->password
            )
        );
        return $query;
    }
}
?>

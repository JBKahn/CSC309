<?php
class Product_model extends CI_Model {

    function getAll() {
        $query = $this->db->get('products');
        return $query->result('Product');
    }

    function get($id) {
        $query = $this->db->get_where('products',array('id' => $id));
        return $query->row(0,'Product');
    }

    function delete($id) {
        return $this->db->delete("products",array('id' => $id ));
    }

    function insert($input) {
        return $this->db->insert(
            "products",
            array(
                'name' => $input['name'],
                'description' => $input['description'],
                'price' => $input['price'],
                'photo_url' => $input['photo_url']
            )
        );
    }

    function update($input) {
        $this->db->where('id', $input['id']);
        return $this->db->update(
            "products",
            array(
                'name' => $input['name'],
                'description' => $input['description'],
                'price' => $input['price']
            )
        );
    }
}
?>

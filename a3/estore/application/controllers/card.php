<?php

class Card extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        if (!$this->_isLoggedIn()) {
            redirect('authentication/login', 'refresh');
            return;
        }
        redirect('store/index', 'refresh');
    }

    function newForm() {
        if (!$this->_isAdmin()) {
            $this->index();
            return;
        }
        $this->_renderPage('card/newForm.php', null);
    }

    function create() {
        if (!$this->_isAdmin()) {
            $this->index();
            return;
        }
        $this->load->library('form_validation');

        $rules = array(
            array('field' => 'name',
                  'label' => 'Name',
                  'rules' => 'required|is_unique[products.name]'
            ),
            array('field' => 'description',
                  'label' => 'Description',
                  'rules' => 'required'
            ),
            array('field' => 'price',
                  'label' => 'Price',
                  'rules' => 'required|numeric'
            )
        );
        $this->form_validation->set_rules($rules);

        $fileUploadSuccess = $this->upload->do_upload();

        if ($this->form_validation->run() == true && $fileUploadSuccess) {
            $data = $this->upload->data();

            $post = $this->input->post();
            $post['photo_url'] = $data['file_name'];

            $this->load->model('product_model');
            $this->product_model->insert($post);

            //Then we redirect to the index page again
            redirect('store/index', 'refresh');
        } else {
            if ( !$fileUploadSuccess) {
                $data['fileerror'] = $this->upload->display_errors();
            }
            $this->_renderPage('card/newForm.php', $data);
        }
    }

    function read($id) {
        if (!$this->_isLoggedIn()) {
            $this->index();
            return;
        }
        $this->load->model('product_model');
        $product = $this->product_model->get($id);
        $data['product']=$product;
        $this->_renderPage('card/read.php', $data);
    }

    function editForm($id) {
        if (!$this->_isadmin()) {
            $this->index();
            return;
        }
        $this->load->model('product_model');
        $product = $this->product_model->get($id);
        $data['product']=$product;
        $this->_renderPage('card/editForm.php', $data);
    }

    function update($id) {
        if (!$this->_isAdmin()) {
            $this->index();
            return;
        }
        $this->load->library('form_validation');
        $rules = array(
            array('field' => 'name',
                  'label' => 'Name',
                  'rules' => 'required'
            ),
            array('field' => 'description',
                  'label' => 'Description',
                  'rules' => 'required'
            ),
            array('field' => 'price',
                  'label' => 'Price',
                  'rules' => 'required|numeric'
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == true) {
            $this->load->model('product_model');
            $post = $this->input->post();
            $post['id'] = $id;

            $this->product_model->update($post);
            //Then we redirect to the index page again
            redirect('store/index', 'refresh');
        } else {
            $product = new Product();
            $product->id = $id;
            $product->name = set_value('name');
            $product->description = set_value('description');
            $product->price = set_value('price');
            $data['product'] = $product;
            $this->_renderPage('card/editForm.php', $data);
        }
    }

    function delete($id) {
        if (!$this->_isadmin()) {
            $this->index();
            return;
        }
        $this->load->model('product_model');

        if (isset($id)) {
            $this->product_model->delete($id);
        }
        //Then we redirect to the index page again
        redirect('store/index', 'refresh');
    }
}
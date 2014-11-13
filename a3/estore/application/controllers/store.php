<?php

class Store extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        if (!$this->_isLoggedIn()) {
            redirect('authentication/login', 'refresh');
            return;
        }
        $this->load->model('product_model');
        $products = $this->product_model->getAll();
        $data['products']=$products;
        $this->_renderPage('card/list.php', $data);
    }
}
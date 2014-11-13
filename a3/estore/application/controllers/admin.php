<?php

class Admin extends MY_Controller {

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

    function viewOrders(){
        if (!$this->_isadmin()) {
            $this->index();
            return;
        }
        $this->load->model('order_model');
        $orders = $this->order_model->getAll();
        $data['orders']=$orders;
        $this->_renderPage('admin/finalizedOrders.php', $data);
    }

    function deleteAll(){
        if (!$this->_isadmin()) {
            $this->index();
            return;
        }
        $this->load->model('order_model');
        $this->load->model('order_item_model');
        $this->load->model('customer_model');

        $orders = $this->order_model->getAll();
        $order_items = $this->order_item_model->getAll();
        $customers = $this->customer_model->getAll();

        foreach ($order_items as $order_item) {
            $this->order_item_model->delete($order_item->id);
        }
        foreach ($orders as $order) {
            $this->order_model->delete($order->id);
        }
        foreach ($customers as $customer) {
            $this->customer_model->delete($customer->id);
        }
        redirect('store/index', 'refresh');
    }
}
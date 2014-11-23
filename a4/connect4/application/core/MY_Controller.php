<?php
class MY_Controller extends CI_Controller
{
    function __construct() {
         // Call the Controller constructor
        parent::__construct();
        session_start();

        $config['upload_path'] = './images/product/';
        $config['allowed_types'] = 'gif|jpg|png';
        $this->load->library('upload', $config);
    }

    function _isAdmin() {
        return $this->_isLoggedIn() && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];
    }

    function _isCustomer() {
        return $this->_isLoggedIn() && isset($_SESSION['isAdmin']) && !$_SESSION['isAdmin'];
    }

    function _isLoggedIn() {
        return isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'];
    }

    function _renderPage($template, $data) {
        $data['main']=$template;
        if ($this->_isAdmin()) {
            $data['loggedInAs'] = "admin";
        } elseif ($this->_isCustomer()) {
            $data['loggedInAs'] = 'customer';
        } else {
            $data['loggedInAs'] = '';
        }
        $this->load->view('main/base.php', $data);
    }
}
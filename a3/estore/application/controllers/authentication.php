<?php

class Authentication extends MY_Controller {

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

    function signUp() {
        $this->_renderPage('authentication/signUp.php', null);
    }

    function addCustomer() {
        $this->load->library('form_validation');
        $rules = array(
            array('field' => 'first',
                  'label' => 'First Name',
                  'rules' => 'required'
            ),
            array('field' => 'last',
                  'label' => 'Last Name',
                  'rules' => 'required'
            ),
            array('field' => 'login',
                  'label' => 'User Name',
                  'rules' => 'required|is_unique[customers.login]'
            ),
            array('field' => 'password',
                  'label' => 'Password',
                  'rules' => 'required|min_length[6]'
            ),
            array('field' => 'email',
                  'label' => 'Email',
                  'rules' => 'required|valid_email'
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == true) {
            $this->load->model('customer_model');
            $this->customer_model->insert($this->input->post());

            redirect('store/index', 'refresh');
        }
        else {
            $customer = new Customer();
            $customer->first = set_value('first');
            $customer->last = set_value('last');
            $customer->login = set_value('login');
            $customer->email = set_value('email');
            $data['customer'] = $customer;
            $this->_renderPage('authentication/signUp.php', $data);
        }
    }

    function logout() {
        if(session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        redirect('authentication/login', 'refresh');
    }

    function login() {
        $_SESSION['isAdmin'] = false;
        $_SESSION['isLoggedIn'] = false;
        $this->load->library('form_validation');

        $rules = array(
            array('field' => 'login',
                  'label' => 'User Name',
                  'rules' => 'required') ,
            array('field' => 'password',
                  'label' => 'Password',
                  'rules' => 'required|callback_authentication_check')
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == true) {
            $login = htmlspecialchars($this->input->get_post('login'));
            $password = htmlspecialchars($this->input->get_post('password'));

            // if user and pass are admin, log in as admin
            if (($login === 'admin') && ($password === 'admin')) {
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['isAdmin'] = true;
                redirect('store/index', 'refresh');
            } else {
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['isAdmin'] = false;
                redirect('store/index', 'refresh');
            }
        } else {
            $this->_renderPage('authentication/login.php', null);

        }
    }

    function authentication_check() {
        $customer = new Customer;
        $customer->login = htmlspecialchars($this->input->get_post('login'));
        $customer->password = htmlspecialchars($this->input->get_post('password'));

        // check database for valid login info
        $this->load->model('customer_model');
        $authenticated = $this->customer_model->isValidLogin($customer);

        if (($customer->login === 'admin') && ($customer->password === 'admin')) {
            return TRUE;
        };
        if ($authenticated->num_rows() > 0) {
            $_SESSION['customerID'] = $authenticated->row()->id;
            return TRUE;
        }

        $this->form_validation->set_message('authentication_check', 'These credentials are invalid.');
        return FALSE;
    }
}

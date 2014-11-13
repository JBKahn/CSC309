<?php

class Store extends CI_Controller {

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

    function renderPage($template, $data) {
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

    function index() {
        if (!$this->_isLoggedIn()) {
            redirect('store/login', 'refresh');
            return;
        }
        $this->load->model('product_model');
        $products = $this->product_model->getAll();
        $data['products']=$products;
        $this->renderPage('product/list.php', $data);
    }

    function newForm() {
        if (!$this->_isAdmin()) {
            $this->index();
            return;
        }
        $this->renderPage('product/newForm.php', null);
    }

    function signUp() {
        $this->renderPage('customer/signUp.php', null);
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
            $this->renderPage('product/newForm.php', $data);
        }
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
            $this->renderPage('customer/signUp.php', $data);
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
        $this->renderPage('product/read.php', $data);
    }

    function editForm($id) {
        if (!$this->_isadmin()) {
            $this->index();
            return;
        }
        $this->load->model('product_model');
        $product = $this->product_model->get($id);
        $data['product']=$product;
        $this->renderPage('product/editForm.php', $data);
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
            $this->renderPage('product/editForm.php', $data);
        }
    }

    function delete($id) {
        if (!$this->_isadmin()) {
            $this->index();
            return;
        }
        $this->load->model('product_model');

        if (isset($id)) {
            $this->produc_model->delete($id);
        }
        //Then we redirect to the index page again
        redirect('store/index', 'refresh');
    }

    function logout() {
        if(session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        redirect('store/login', 'refresh');
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
            $this->renderPage('main/login.php', null);

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

    function cart(){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }
        $total = 0;
        $products = array();
        $items = array();

        if (isset($_SESSION['cart'])) {
            $items = unserialize($_SESSION['cart']);
            $this->load->model('product_model');
            foreach ($items as $item) {
                $product = $this->product_model->get($item->product_id);
                $products[] = $product;
                $total+= $item->quantity * $product->price;
            }
        }

        $data['items'] = $items;
        $data['products'] = $products;
        $data['total'] = $total;
        $_SESSION['total'] = $total;
        $this->renderPage('checkout/displayCart.php', $data);
    }

    function adjustProductInCart($id, $difference, $removeAll) {
        $productInCart = false;
        $items = unserialize($_SESSION['cart']);
        for ($index = 0; $index<count($items); $index++) {
            if ($items[$index]->product_id == $id) {
                $items[$index]->quantity += $difference;
                $productInCart = true;
                if ($items[$index]->quantity <= 0 || $removeAll) {
                    unset($items[$index]);
                    $items = array_values($items);
                }
                break;
            }
        }
        if (!$productInCart && $difference > 0) {
            $item = new Order_item;
            $item->product_id = $id;
            $item->quantity = 1;
            $items[] = $item;
        }
        $_SESSION['cart'] = serialize($items);
    }

    function addOneProductToCart($id){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }
        if (!isset($_SESSION['cart']) || !$_SESSION['cart']) {
            // Initialize the cart.
            $item = new Order_item;
            $item->product_id = $id;
            $item->quantity = 1;
            $_SESSION['cart'] = serialize(array($item));
        } else {
            $this->adjustProductInCart($id, 1, false);
        }
        $this->cart();
    }

    function removeOneProductFromCart($id){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }
        $this->adjustProductInCart($id, -1, false);
        $this->cart();
    }

    function removeProductFromCart($id){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }
        $this->adjustProductInCart($id, 0, true);
        $this->cart();
    }

    function checkout(){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }

        if (!isset($_SESSION['cart']) || !$_SESSION['cart']) {
            $this->cart();
            return;
        }

        $this->renderPage('checkout/creditCardForm.php', null);
    }

    function check_valid_expiry() {
        $month = $this->input->post('creditcard_month');
        $year = $this->input->post('creditcard_year');

        $expiredPreviousYear = $year < date("y", time());
        $expiredThisYear = ($year == date("y", time())) && ($month <= date("m", time()));

        if ($expiredPreviousYear || $expiredThisYear) {
            $this->form_validation->set_message('check_valid_expiry', 'This credit card is expired.');
            return FALSE;
        }
        return TRUE;
    }

    function payForm(){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }

        if (!isset($_SESSION['cart']) || !$_SESSION['cart']) {
            $this->cart();
            return;
        }

        $this->load->library('form_validation');

        $rules = array(
            array('field' => 'creditcard_number',
                  'label' => 'Credit Card Number',
                  'rules' => 'required|numeric|min_length[16]|max_length[16]'
            ),
            array('field' => 'creditcard_month',
                  'label' => 'Expiry Month (MM)',
                  'rules' => 'required|numeric|less_than[13]|greater_than[0]'
            ),
            array('field' => 'creditcard_year',
                  'label' => 'Expiry Year (YY)',
                  'rules' => 'required|numeric|less_than[100]|greater_than[0]|callback_check_valid_expiry'
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == true) {
            $this->load->model('order_model');
            $this->load->model('order_item_model');
            $this->load->model('product_model');

            $order = new Order();
            $order->customer_id = $_SESSION['customerID'];
            $order->order_date = date("Y-m-d", time());
            $order->order_time = date("G:i:s", time());
            $order->total = $_SESSION['total'];
            $order->creditcard_number =  (int) ($this->input->get_post('creditcard_number'));
            $order->creditcard_month =  (int) ($this->input->get_post('creditcard_month'));
            $order->creditcard_year =  (int) ($this->input->get_post('creditcard_year'));

            $order_id = $this->order_model->insert($order);

            $quantity = array();
            $name = array();
            $price = array();
            $items = unserialize($_SESSION['cart']);
            foreach ($items as $item) {
                $item->order_id = $order_id;
                $this->order_item_model->insert($item);
                $product = $this->product_model->get($item->product_id);
                $quantity[] = $item->quantity;
                $name[] = $product->name;
                $price[] = $product->price;
            }

            // empty  the cart
            unset($_SESSION['cart']);

            $data['quantity'] = $quantity;
            $data['name'] = $name;
            $data['price'] = $price;
            $data['order_id'] = $order_id;
            $data['total'] = $order->total;

            // send mail
            $this->load->model('customer_model');
            $customer = $this->customer_model->get($order->customer_id);
            if ($customer->email) {
                // Commenting out as suggested
                $message = $this->load->view('checkout/emailReceiptToCustomer.php', $data, TRUE);
                $this->load->library('email');
                $this->email->from('orlykahnmakeupartist@gmail.com', 'Baseball Card Store');
                $this->email->to($customer->email);
                $this->email->subject('Your Baseball Card Reciept');
                $this->email->message($message);
                $this->email->send();
                echo $this->email->print_debugger();
            }
            $this->renderPage('checkout/orderReciept.php', $data);
        }
        else {
            $this->renderPage('checkout/creditCardForm.php', null);
        }
    }

    function viewOrders(){
        if (!$this->_isadmin()) {
            $this->index();
            return;
        }
        $this->load->model('order_model');
        $orders = $this->order_model->getAll();
        $data['orders']=$orders;
        $this->renderPage('main/finalizedOrders.php', $data);
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

<?php

class Cart extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        if (!$this->_isLoggedIn()) {
            redirect('authentication/login', 'refresh');
            return;
        }
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
        $this->_renderPage('cart/displayCart.php', $data);
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
        $this->index();
    }

    function removeOneProductFromCart($id){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }
        $this->adjustProductInCart($id, -1, false);
        $this->index();
    }

    function removeProductFromCart($id){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }
        $this->adjustProductInCart($id, 0, true);
        $this->index();
    }

    function checkout(){
        if (!$this->_isCustomer()) {
            $this->index();
            return;
        }

        if (!isset($_SESSION['cart']) || !$_SESSION['cart']) {
            $this->index();
            return;
        }

        $this->_renderPage('cart/creditCardForm.php', null);
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
            $this->index();
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
                $message = $this->load->view('cart/emailReceiptToCustomer.php', $data, TRUE);
                $this->load->library('email');
                $this->email->from('orlykahnmakeupartist@gmail.com', 'Baseball Card Store');
                $this->email->to($customer->email);
                $this->email->subject('Your Baseball Card Reciept');
                $this->email->message($message);
                $this->email->send();
                echo $this->email->print_debugger();
            }
            $this->_renderPage('cart/orderReciept.php', $data);
        }
        else {
            $this->_renderPage('cart/creditCardForm.php', null);
        }
    }
}

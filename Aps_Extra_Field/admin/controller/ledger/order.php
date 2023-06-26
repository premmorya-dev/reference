<?php
class ControllerLedgerOrder extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('ledger/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ledger/order');

		$this->getList();
	}



	protected function getList()
	{



		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->get['filter_customer_tel'])) {
			$filter_customer_tel = $this->request->get['filter_customer_tel'];
		} else {
			$filter_customer_tel = '';
		}

		if (isset($this->request->get['filter_order_total'])) {
			$filter_order_total = $this->request->get['filter_order_total'];
		} else {
			$filter_order_total = '';
		}

		if (isset($this->request->get['filter_payment_status'])) {
			$filter_payment_status = $this->request->get['filter_payment_status'];
		} else {
			$filter_payment_status = '';
		}

		if (isset($this->request->get['filter_payment_processor'])) {
			$filter_payment_processor = $this->request->get['filter_payment_processor'];
		} else {
			$filter_payment_processor = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int) $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . urlencode(html_entity_decode($this->request->get['filter_order_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_tel'])) {
			$url .= '&filter_customer_tel=' . $this->request->get['filter_customer_tel'];
		}

		if (isset($this->request->get['filter_order_total'])) {
			$url .= '&filter_order_total=' . $this->request->get['filter_order_total'];
		}

		if (isset($this->request->get['filter_payment_status'])) {
			$url .= '&filter_payment_status=' . $this->request->get['filter_payment_status'];
		}

		if (isset($this->request->get['filter_payment_processor'])) {
			$url .= '&filter_payment_processor=' . $this->request->get['filter_payment_processor'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('ledger/order/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['delete'] = $this->url->link('ledger/order/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_order_id' => $filter_order_id,
			'filter_email' => $filter_email,
			'filter_customer_tel' => $filter_customer_tel,
			'filter_order_total' => $filter_order_total,
			'filter_payment_status' => $filter_payment_status,
			'filter_payment_processor' => $filter_payment_processor,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		

		

		$payment_total = $this->model_ledger_order->getTotalAllPayment($filter_data);
	    $all_orders_payments = $this->model_ledger_order->getAllOrderPayment($filter_data);
		
		if ($all_orders_payments) {
			foreach ($all_orders_payments as $all_orders_payment) {

				if( isset($all_orders_payment['payment']['payment_date']) && $all_orders_payment['payment']['payment_date'] && $all_orders_payment['order_date']){

					$all_orders_payment['payment']['payment_date']= date('d-M-Y h:i:A',$all_orders_payment['payment']['payment_date']);

				}
				$data['all_orders_payments'][] = array(
					'order_id' => $all_orders_payment['order_id'],
					'order_date' => $all_orders_payment['order_date'],
					'ledger_id' => $all_orders_payment['ledger_id'],
					'customer_id' => $all_orders_payment['customer_id'],
					'email' => $all_orders_payment['email'],
					'telephone' => $all_orders_payment['telephone'],
					'total' => $all_orders_payment['total'],
					'transaction_id' => $all_orders_payment['transaction_id'],
					'transaction_type' => $all_orders_payment['transaction_type'],
					'amount' => $all_orders_payment['amount'],
					'description' => $all_orders_payment['description'],
					'processor_type' => $all_orders_payment['processor_type'],
					'transaction_log_id' => $all_orders_payment['transaction_log_id'],
					'payment' => $all_orders_payment['payment'],				
					'edit' => $this->url->link('ledger/order/transaction', 'user_token=' . $this->session->data['user_token'] . '&transaction_id=' . $all_orders_payment['transaction_id'] . $url, true)
				);

			}
		}


		// $dat	a['user_token'] = $this->session->data['user_token'];

		// if (isset($this->error['warning'])) {
		// 	$data['error_warning'] = $this->error['warning'];
		// } else {
		// 	$data['error_warning'] = '';
		// }

		// if (isset($this->session->data['success'])) {
		// 	$data['success'] = $this->session->data['success'];

		// 	unset($this->session->data['success']);
		// } else {
		// 	$data['success'] = '';
		// }

		// if (isset($this->request->post['selected'])) {
		// 	$data['selected'] = (array) $this->request->post['selected'];
		// } else {
		// 	$data['selected'] = array();
		// }

		// $url = '';

		// if (isset($this->request->get['filter_name'])) {
		// 	$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		// }

		// if (isset($this->request->get['filter_model'])) {
		// 	$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		// }

		// if (isset($this->request->get['filter_price'])) {
		// 	$url .= '&filter_price=' . $this->request->get['filter_price'];
		// }

		// if (isset($this->request->get['filter_quantity'])) {
		// 	$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		// }

		// if (isset($this->request->get['filter_status'])) {
		// 	$url .= '&filter_status=' . $this->request->get['filter_status'];
		// }

		// if ($order == 'ASC') {
		// 	$url .= '&order=DESC';
		// } else {
		// 	$url .= '&order=ASC';
		// }

		// if (isset($this->request->get['page'])) {
		// 	$url .= '&page=' . $this->request->get['page'];
		// }

		// $data['sort_name'] = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		// $data['sort_model'] = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		// $data['sort_price'] = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		// $data['sort_quantity'] = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		// $data['sort_status'] = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		// $data['sort_order'] = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		// $url = '';



		// if (isset($this->request->get['filter_transaction_id_from'])) {
		// 	$url .= '&filter_transaction_id_from=' . $this->request->get['filter_transaction_id_from'];
		// }

		// if (isset($this->request->get['filter_transaction_id_to'])) {
		// 	$url .= '&filter_transaction_id_to=' . $this->request->get['filter_transaction_id_to'];
		// }

		// if (isset($this->request->get['filter_amount'])) {
		// 	$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		// }

		// if (isset($this->request->get['sort'])) {
		// 	$url .= '&sort=' . $this->request->get['sort'];
		// }

		// if (isset($this->request->get['order'])) {
		// 	$url .= '&order=' . $this->request->get['order'];
		// }

		// $pagination = new Pagination();
		// $pagination->total = $payment_total;
		// $pagination->page = $page;
		// $pagination->limit = $this->config->get('config_limit_admin');
		// $pagination->url = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		// $data['pagination'] = $pagination->render();

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($payment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($payment_total - $this->config->get('config_limit_admin'))) ? $payment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $payment_total, ceil($payment_total / $this->config->get('config_limit_admin')));
		$data['user_token'] = $this->session->data['user_token'];
		$data['filter_order_id'] = $filter_order_id;
		$data['filter_email'] = $filter_email;
		$data['filter_customer_tel'] = $filter_customer_tel;
		$data['filter_order_total'] = $filter_order_total;
		$data['filter_payment_status'] = $filter_payment_status;
		$data['filter_payment_processor'] = $filter_payment_processor;
	

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ledger/order_list', $data));
	}

	public function add()
	{


		$this->load->language('ledger/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ledger/order');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['user_token'] = $this->session->data['user_token'];
		$data['back'] = $this->url->link('ledger/order', 'user_token=' . $this->session->data['user_token'] , true);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ledger/add_ledger_order_transaction', $data));

	}

	public function orderList() { 


		$json = [];
		$this->load->model('ledger/order');	
		$customer = $this->model_ledger_order->getCustomer($this->request->get['email']);
		
		$customer_orders = $this->model_ledger_order->getOrderById($customer['customer_id']);
		
		$json['order_amount'] = '';
		$json['order_amount'] = "<option value='' amount='' > --- Please Select --- </option>";
		if($customer_orders){			
			foreach($customer_orders as $customer_order){				
				$json['order_amount'] .="<option value='" . $customer_order['order_id'] .  "'amount='". $customer_order['total']   ."'>" . $customer_order['order_id']  . " | ". $customer_order['total']  ." | ". $customer_order['date_added'] ."</option>";
			}
		}
		$json['error'] = 0;	
		
		if($customer){
			$json['error'] = 0;
			$json['message']  = "<div style='margin-top: 50px;color: green; margin-left: 20px;' ><h4 >Transaction Added Successfully! </h4></div>" ;		
		}else{
			$json['error'] = 1;
			$json['message']  = "<div style='margin-top: 50px;color: red; margin-left: 20px;' ><h4 >** Something went wrong. Please try again </h4></div>" ;			
		}
		$this->response->setOutput( json_encode($json) );
	}


	public function unsettledTransaction() { 

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$this->load->language('ledger/order');
		$json = [];
		$this->load->model('ledger/order');	
		$unsetteled_transactions = $this->model_ledger_order->unsettledTransaction($this->request->get['customer_id']);
		
		if($unsetteled_transactions ){
			foreach( $unsetteled_transactions as $unsetteled_transaction){
                  $data['unsetteled_transactions'][] = [
					"ledger_id" => $unsetteled_transaction['ledger_id'],
					"transaction_log_id" => $unsetteled_transaction['transaction_log_id'],
					"transaction_id" =>  $unsetteled_transaction['transaction_id'],  
					"processor_type" => strtoupper( $unsetteled_transaction['processor_type']),       
					"ref_no" =>  $unsetteled_transaction['ref_no'],
					"method" => strtoupper($unsetteled_transaction['method']),
					"amount" =>  $unsetteled_transaction['amount'],
					"transaction_date" =>  $unsetteled_transaction['transaction_date'],
				  ];
			}
		}
		$data['update'] = $this->url->link('ledger/order/updateLedger', 'user_token=' . $this->session->data['user_token'], true);

		$this->response->setOutput($this->load->view('ledger/unsetteled_transaction', $data));
		
	}


	public function updateLedger() { 
		$this->load->language('ledger/order');
		$json = [];
		$this->load->model('ledger/order');	
		$this->model_ledger_order->updateLedger($this->request->post);
		$json['message']  ="<div class=\"alert alert-success alert-dismissible\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><strong>Success!</strong> Ledger Id assigned to orders successfully. </div>";
		  $this->response->setOutput( json_encode($json) );
		
	}

}
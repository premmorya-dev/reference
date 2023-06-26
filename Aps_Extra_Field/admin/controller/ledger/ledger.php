<?php
class ControllerLedgerLedger extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('ledger/ledger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ledger/ledger');

		$this->getList();
	}

	public function add()
	{
		$this->load->language('ledger/ledger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ledger/ledger');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ledger_ledger->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit()
	{
		$this->load->language('ledger/ledger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ledger/ledger');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_ledger_ledger->editProduct($this->request->get['product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('ledger/ledger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ledger/ledger');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_ledger_ledger->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}



	protected function getList()
	{



		if (isset($this->request->get['filter_transaction_id_from'])) {
			$filter_transaction_id_from = $this->request->get['filter_transaction_id_from'];
		} else {
			$filter_transaction_id_from = '';
		}

		if (isset($this->request->get['filter_transaction_id_to'])) {
			$filter_transaction_id_to = $this->request->get['filter_transaction_id_to'];
		} else {
			$filter_transaction_id_to = '';
		}

		if (isset($this->request->get['filter_amount'])) {
			$filter_amount = $this->request->get['filter_amount'];
		} else {
			$filter_amount = '';
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

		if (isset($this->request->get['filter_transaction_id_from'])) {
			$url .= '&filter_transaction_id_from=' . urlencode(html_entity_decode($this->request->get['filter_transaction_id_from'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_transaction_id_to'])) {
			$url .= '&filter_transaction_id_to=' . urlencode(html_entity_decode($this->request->get['filter_transaction_id_to'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
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
			'href' => $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('ledger/ledger/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['delete'] = $this->url->link('ledger/ledger/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_transaction_id_from' => $filter_transaction_id_from,
			'filter_transaction_id_to' => $filter_transaction_id_to,
			'filter_amount' => $filter_amount,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');



		$payment_total = $this->model_ledger_ledger->getTotalAllPayment($filter_data);
		$all_payments = $this->model_ledger_ledger->getAllPayment($filter_data);
		// print_r($all_payments );die;
		if ($all_payments) {
			foreach ($all_payments as $payment) {

				$data['all_payments'][] = array(
					'ledger_id' => $payment['ledger_id'],
					'transaction_id' => $payment['transaction_id'],
					'transaction_type' => $payment['transaction_type'],
					'amount' => $payment['amount'],
					'description' => $payment['description'],
					'edit' => $this->url->link('ledger/ledger/transaction', 'user_token=' . $this->session->data['user_token'] . '&transaction_id=' . $payment['transaction_id'] . $url, true)
				);

			}
		}


		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';



		if (isset($this->request->get['filter_transaction_id_from'])) {
			$url .= '&filter_transaction_id_from=' . $this->request->get['filter_transaction_id_from'];
		}

		if (isset($this->request->get['filter_transaction_id_to'])) {
			$url .= '&filter_transaction_id_to=' . $this->request->get['filter_transaction_id_to'];
		}

		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $payment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($payment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($payment_total - $this->config->get('config_limit_admin'))) ? $payment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $payment_total, ceil($payment_total / $this->config->get('config_limit_admin')));

		$data['filter_transaction_id_from'] = $filter_transaction_id_from;
		$data['filter_transaction_id_to'] = $filter_transaction_id_to;
		$data['filter_amount'] = $filter_amount;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ledger/ledger_list', $data));
	}

	public function view()
	{


		$this->load->language('ledger/ledger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ledger/ledger');

		$url = '';

		if (isset($this->request->get['filter_rp_transaction_id'])) {
			$url .= '&filter_rp_transaction_id=' . urlencode(html_entity_decode($this->request->get['filter_rp_transaction_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_id'])) {
			$url .= '&filter_oc_id=' . urlencode(html_entity_decode($this->request->get['filter_oc_id'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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
			'href' => $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (isset($this->request->get['transaction_log_id'])) {
			$transaction_log_id = $this->request->get['transaction_log_id'];
		} else {
			$transaction_log_id = '';
		}
		$data['payment'] = $this->model_ledger_ledger->getPayment($transaction_log_id);
		if (isset($data['payment']['created_at']) && $data['payment']['created_at']) {
			$data['payment']['created_at'] = date('d-M-Y h:i:A', $data['payment']['created_at']);
		}

		$data['back'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ledger/ledger_view', $data));

	}

	public function transaction()
	{
		if( isset($this->request->get['transaction_id']) && $this->request->get['transaction_id']){
			$transaction_id = $this->request->get['transaction_id'];
		}else{
			$transaction_id = 0 ;
		}
		$this->load->model('ledger/ledger');
	
		$data['credit_transactions'] = $this->model_ledger_ledger->getLedgerCreditTransaction($transaction_id );
		$data['debit_transactions'] = $this->model_ledger_ledger->getLedgerDebitTransaction($transaction_id );
		$data['debit_transactions_total'] = $this->model_ledger_ledger->getLedgerTotalDebitTransaction($transaction_id );
//   print_r(	$data['debit_transactions_total']);die;

		$data['user_token'] = $this->session->data['user_token'];
		$data['edit'] = $this->url->link('ledger/ledger/editTransaction&transaction_id='. $transaction_id , 'user_token=' . $this->session->data['user_token'] , true);
		$data['cancel'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] , true);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ledger/ledger_transaction', $data));
	}

	public function editTransaction()
	{
		if( isset($this->request->get['transaction_id']) && $this->request->get['transaction_id']){
			$transaction_id = $this->request->get['transaction_id'];
		}else{
			$transaction_id = 0 ;
		}
		$this->load->model('ledger/ledger');
	
		$data['credit_transactions'] = $this->model_ledger_ledger->getLedgerCreditTransaction($transaction_id );
		$data['debit_transactions'] = $this->model_ledger_ledger->getLedgerDebitTransaction($transaction_id );
		$data['debit_transactions_total'] = $this->model_ledger_ledger->getLedgerTotalDebitTransaction($transaction_id );
        //    print_r(	$data['debit_transactions']);die;

		$data['user_token'] = $this->session->data['user_token'];
		$data['cancel'] = $this->url->link('ledger/ledger', 'user_token=' . $this->session->data['user_token'] , true);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ledger/edit_ledger_transaction', $data));
	}

	public function updateTransaction()
	{
    
		$json = [];
		$json['error'] = 0;
		if( isset($this->request->post['ledger']) && $this->request->post['ledger'] ){
			$json_data =  html_entity_decode($this->request->post['ledger']);
			// print_r( $json_data  );die;
			$ledger = json_decode( $json_data ,  1 );
			
		}else{
			$ledger = [] ;
		}
		
	

		if( $this->request->post['credit_amount'] != $this->request->post['debit_total'] ){
			$json['error'] = 1;
			$json['message'] = 'Debit total amount and credit total amount miss-match';
		}else{
			$this->load->model('ledger/ledger');	
			$this->model_ledger_ledger->updateLedgerDebitTransaction($ledger );
			$json['error'] = 0;
			$json['message'] = 'You have been modified debit transaction successfully';
		}
		
	
		
		
        // print_r($json);
		$this->response->setOutput(json_encode($json));
	}

	public function deleteTransaction()
	{
// print_r($this->request->post);die;
		$json = [];
		if( isset($this->request->post['ledger_id']) && $this->request->post['ledger_id'] ){
			$ledger_id =  html_entity_decode($this->request->post['ledger_id']);			
			
		}else{
			$ledger_id = '';
		}
		
		$this->load->model('ledger/ledger');
	
		$this->model_ledger_ledger->deleteLedgerDebitTransaction($ledger_id );
	
		$json['message'] = 'You have been modified debit transaction successfully';
		
		$this->response->setOutput(json_encode($json));
	}
	protected function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'ledger/ledger')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['product_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if ($this->request->post['product_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['product_id']) || (($seo_url['query'] != 'product_id=' . $this->request->get['product_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

								break;
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'ledger/ledger')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy()
	{
		if (!$this->user->hasPermission('modify', 'ledger/ledger')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete()
	{
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('ledger/ledger');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int) $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name' => $filter_name,
				'filter_model' => $filter_model,
				'start' => 0,
				'limit' => $limit
			);

			$results = $this->model_ledger_ledger->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_ledger_ledger->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id' => $product_option_value['option_value_id'],
									'name' => $option_value_info['name'],
									'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix' => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id' => $product_option['option_id'],
							'name' => $option_info['name'],
							'type' => $option_info['type'],
							'value' => $product_option['value'],
							'required' => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model' => $result['model'],
					'option' => $option_data,
					'price' => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getTransaction()
	{
		$this->load->model('ledger/ledger');
		$json = [];
		$json['error'] = 0;
		if (isset($this->request->get['transaction_id']) && $this->request->get['transaction_id']) {
			$transaction_id = $this->request->get['transaction_id'];
		} else {
			$transaction_id = 0;
		}
		$is_payment_exist = $this->model_ledger_ledger->isPaymentExist($transaction_id);
		if ($is_payment_exist) {
			$json['error'] = 1;
			$json['message'] = "<div style='margin-top: 50px;color: red; margin-left: 20px;' ><h4 >** Payment already exist</h4></div>";
			$this->response->setOutput(json_encode($json));
			return;

		}

		if (isset($transaction_id) && $transaction_id) {

			$api = new Api($this->config->get('payment_razorpay_key_id'), $this->config->get('payment_razorpay_key_secret'));
			try {
				$payments = (array) $api->payment->fetch($transaction_id);
			} catch (\Razorpay\Api\Errors\Error $e) {
				$json['error'] = 1;
				$json['message'] = "<div style='margin-top: 50px;color: red; margin-left: 20px;' ><h4 >** " . $e->getMessage() . "</h4></div>";
				$this->response->setOutput(json_encode($json));
				$this->log->write("Razorpay payment error: " . $e->getMessage());
				return;
			}
		}
		$data['payment'] = $this->model_ledger_ledger->getTransaction($transaction_id);

		$json['method'] = $data['payment']['method'];
		$data['user_token'] = $this->session->data['user_token'];

		$json['message'] = $this->load->view('ledger/ledger_transaction', $data);
		$this->response->setOutput(json_encode($json));


	}

	public function addPayment()
	{
		$json = [];
		$this->load->model('ledger/ledger');

		$added = $this->model_ledger_ledger->addPayment($this->request->post);
		$json['error'] = 0;

		if ($this->request->post['payment_amount_value'] && $this->request->post['order_amount_value']) {

			if ($this->request->post['order_amount_value'] < $this->request->post['payment_amount_value']) {
				$json['notes'] = "<div style='margin-top: 20px;color: #6091cd;margin-left: 20px;'><h4>Order amount and payment amount missmatch transaction can be added to the customer account without order confirmation. </br> Order can be confirmed manually.</h4></div>";
			}

		}

		if ($added) {
			$json['error'] = 0;
			$json['message'] = "<div style='margin-top: 50px;color: green; margin-left: 20px;' ><h4 >Transaction Added Successfully! </h4></div>";
		} else {
			$json['error'] = 1;
			$json['message'] = "<div style='margin-top: 50px;color: red; margin-left: 20px;' ><h4 >** Something went wrong. Please try again </h4></div>";
		}
		$this->response->setOutput(json_encode($json));
	}



	public function orderList()
	{


		$json = [];
		$this->load->model('ledger/ledger');
		$customer = $this->model_ledger_ledger->getCustomer($this->request->get['email']);

		$customer_orders = $this->model_ledger_ledger->getOrderById($customer['customer_id']);

		$json['order_amount'] = '';
		$json['order_amount'] = "<option value='' amount='' > --- Please Select --- </option>";
		if ($customer_orders) {
			foreach ($customer_orders as $customer_order) {
				$json['order_amount'] .= "<option value='" . $customer_order['order_id'] . "'amount='" . $customer_order['total'] . "'>" . $customer_order['order_id'] . " | " . $customer_order['total'] . " | " . $customer_order['date_added'] . "</option>";
			}
		}
		$json['error'] = 0;

		if ($customer) {
			$json['error'] = 0;
			$json['message'] = "<div style='margin-top: 50px;color: green; margin-left: 20px;' ><h4 >Transaction Added Successfully! </h4></div>";
		} else {
			$json['error'] = 1;
			$json['message'] = "<div style='margin-top: 50px;color: red; margin-left: 20px;' ><h4 >** Something went wrong. Please try again </h4></div>";
		}
		$this->response->setOutput(json_encode($json));
	}


}
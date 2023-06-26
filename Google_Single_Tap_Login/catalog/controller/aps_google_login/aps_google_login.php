<?php
class ControllerApsGoogleLoginApsGoogleLogin extends Controller
{
	public function index()
	{

		$status = $this->addCustomerByGoogle($this->request->post);
		if ($status) {
			$this->response->setOutput(json_encode(['message' => 'success']));
		}
	}


	public function addCustomerByGoogle($data)
	{

		$this->load->model('account/customer');
		$this->load->model('account/customer_group');

		// $customer_group_id = $this->config->get('module_aps_google_login_customer_group');
		$customer_group_id = 1;
		$data['firstname'] = $data['response']['given_name'];
		$data['lastname'] = $data['response']['family_name'];
		$data['email'] = $data['response']['email'];
		$data['telephone'] = '';
		$data['password'] = rand(0, 9999);

		$customer_exist = $this->model_account_customer->getCustomerByEmail($data['email']);

		if ( !isset($customer_exist['customer_id']) || !$customer_exist['customer_id']) {

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

			$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int) $customer_group_id . "', store_id = '" . (int) $this->config->get('config_store_id') . "', language_id = '" . (int) $this->config->get('config_language_id') . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', newsletter = '" . (isset($data['newsletter']) ? (int) $data['newsletter'] : 0) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '" . (int) $customer_group_info['approval'] . "', date_added = NOW()");

			$customer_id = $this->db->getLastId();

			if ($customer_group_info['approval']) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int) $customer_id . "', type = 'customer', date_added = NOW()");
			}

			$this->customer->login($data['email'], '', true);
			// if($customer_id){

			// 	$this->model_account_customer->editCode($data['email'], token(40));
			// }

			return $customer_id;

		}else{
			$this->customer->login($data['email'], '', true);
		}


    return true;
	}



}
<?php
class ControllerExtensionModuleApsGoogleLogin extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/module/aps_google_login');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('module_aps_google_login', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/aps_google_login', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/aps_google_login', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_aps_google_login_status'])) {
			$data['module_aps_google_login_status'] = $this->request->post['module_aps_google_login_status'];
		} else {
			$data['module_aps_google_login_status'] = $this->config->get('module_aps_google_login_status');
		}

		if (isset($this->request->post['module_aps_google_login_client_id'])) {
			$data['module_aps_google_login_client_id'] = $this->request->post['module_aps_google_login_client_id'];
		} else {
			$data['module_aps_google_login_client_id'] = $this->config->get('module_aps_google_login_client_id');
		}

		if (isset($this->request->post['module_aps_google_login_secret_id'])) {
			$data['module_aps_google_login_secret_id'] = $this->request->post['module_aps_google_login_secret_id'];
		} else {
			$data['module_aps_google_login_secret_id'] = $this->config->get('module_aps_google_login_secret_id');
		}

		if (isset($this->request->post['module_aps_google_login_customer_group'])) {
			$data['module_aps_google_login_customer_group'] = $this->request->post['module_aps_google_login_customer_group'];
		} else {
			$data['module_aps_google_login_customer_group'] = $this->config->get('module_aps_google_login_customer_group');
		}


		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups([]);
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/aps_google_login', $data));
	}


	public function install()
	{
		$this->load->model('extension/module/aps_google_login');
		$this->load->model('setting/setting');

		$this->model_extension_module_aps_google_login->install();

	}


	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/aps_google_login')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}

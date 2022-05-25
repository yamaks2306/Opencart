<?php

use ApironeApi\Apirone;

require_once(DIR_SYSTEM . 'library/apirone_api/Apirone.php');

function pa($mixed, $msg = false) {
    echo '<pre>';
    if ($msg)
        echo $msg . ': ';
    print_r($mixed);
    echo '</pre>';
}

class ControllerExtensionPaymentApironeMccp extends Controller {
    private $error = array();

    public function index() {

        $this->load->language('extension/payment/apirone_mccp');
        $this->load->model('extension/payment/apirone_mccp');

        $account = unserialize( $this->config->get('payment_apirone_mccp_account') );
        $secret = $this->config->get('payment_apirone_mccp_secret');

        $apirone_currencies = Apirone::currencyList();
        $plugin_currencies = unserialize( $this->config->get('payment_apirone_mccp_currencies') );

        $errors_count = 0;
        $active_currencies = 0;

        if (!$this->user->hasPermission('modify', 'extension/payment/apirone_mccp')) {
            $data['error'] = $this->language->get('error_permission');
            $errors_count++;
        }

        foreach ($apirone_currencies as $item) {
            $currency = new \stdClass();

            $currency->name = $item->name;
            $currency->abbr = $item->abbr;
            $currency->{'dust-rate'} = $item->{'dust-rate'};
            $currency->{'units-factor'} = $item->{'units-factor'};
            $currency->address = '';
            $currency->currency_tooltip = sprintf($this->language->get('currency_activate_tooltip'), $item->name);
            $currency->testnet = $item->testnet;

            // Set address from config 
            if ($plugin_currencies) {
                $currency->address = $plugin_currencies[$item->abbr]->address;
            }
            // Set address from config 
            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                $currency->address = $_POST['payment_address'][$item->abbr];
                if ($currency->address != '') {
                    $result = Apirone::setTransferAddress($account, $item->abbr, $currency->address);
                        if ($result == false) {
                        $currency->error = 1;
                        $errors_count++;
                    }
                }
            }
            // Set tooltip
            if (empty($currency->address))
                $currency->currency_tooltip = sprintf($this->language->get('currency_activate_tooltip'), $item->name);
            else {
                $currency->currency_tooltip = sprintf($this->language->get('currency_deactivate_tooltip'), $item->name);
                $active_currencies++;
            }
            $currencies[$item->abbr] = $currency;
        }

        // Set values into remplate vars
        $this->setValue($data, 'payment_apirone_mccp_timeout', true);
        $this->setValue($data, 'payment_apirone_mccp_success_status_id');
        $this->setValue($data, 'payment_apirone_mccp_pending_status_id');
        $this->setValue($data, 'payment_apirone_mccp_voided_status_id');
        $this->setValue($data, 'payment_apirone_mccp_geo_zone_id');
        $this->setValue($data, 'payment_apirone_mccp_status');
        $this->setValue($data, 'payment_apirone_mccp_sort_order');
        $this->setValue($data, 'payment_apirone_mccp_merchantname');
        $this->setValue($data, 'payment_apirone_mccp_secret');
        $this->setValue($data, 'payment_apirone_mccp_testcustomer');

        $errors_count = $errors_count + count($this->error);

        // Save settings if post & no errors
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST' && $errors_count == 0)) {
            
            $_settings['payment_apirone_mccp_account'] = serialize($account);
            $_settings['payment_apirone_mccp_secret'] = $secret;
            $_settings['payment_apirone_mccp_currencies'] = serialize($currencies);

            $_settings['payment_apirone_mccp_timeout'] = $_POST['payment_apirone_mccp_timeout'];
            $_settings['payment_apirone_mccp_success_status_id'] = $_POST['payment_apirone_mccp_success_status_id'];
            $_settings['payment_apirone_mccp_pending_status_id'] = $_POST['payment_apirone_mccp_pending_status_id'];
            $_settings['payment_apirone_mccp_voided_status_id'] = $_POST['payment_apirone_mccp_voided_status_id'];
            $_settings['payment_apirone_mccp_geo_zone_id'] = $_POST['payment_apirone_mccp_geo_zone_id'];
            $_settings['payment_apirone_mccp_status'] = $_POST['payment_apirone_mccp_status'];
            $_settings['payment_apirone_mccp_sort_order'] = $_POST['payment_apirone_mccp_sort_order'];
            $_settings['payment_apirone_mccp_merchantname'] = $_POST['payment_apirone_mccp_merchantname'];
            $_settings['payment_apirone_mccp_testcustomer'] = $_POST['payment_apirone_mccp_testcustomer'];

            $this->model_setting_setting->editSetting('payment_apirone_mccp', $_settings);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));

        }

        // =============================================================================================
        // Set template variables

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();


        for ($i= 0; $i<10; $i++)
            $data['confirmations'][$i] = $i+1;

        $data['currencies'] = $currencies;
        
        if (($active_currencies > 0))
            $data['error_empty_currencies'] = false;

        $this->getBreadcrumbsAndActions($data);
        $data['errors'] = $this->error;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/apirone_mccp', $data));
    }

    protected function validate() {
        
    }

    protected function getBreadcrumbsAndActions(&$data) {
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/apirone_mccp', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/apirone_mccp', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
    }

    protected function setValue(&$data, $value, $required = false) {
        if (isset($this->request->post[$value])) {
            $data[$value] = $this->request->post[$value];
        }
        else {
            $data[$value] = $this->config->get($value);
        }
        if ($required && empty($data[$value])) {
            $this->error[$value] = $this->language->get(str_replace('payment', 'error', $value));
        }
    }

    // Install / Uninstall plugin
    public function install() {

        $this->load->model('extension/payment/apirone_mccp');
        $this->load->model('setting/setting');

        $data = array(
            'payment_apirone_mccp_secret' => md5(time().$this->session->data['user_token']),
            'payment_apirone_mccp_pending_status_id' => '1',
            'payment_apirone_mccp_success_status_id' => '5',
            'payment_apirone_mccp_voided_status_id' => '16',
            'payment_apirone_mccp_timeout' => '1800',
            'payment_apirone_mccp_sort_order' => '0');

        $account = Apirone::accountCreate();
        if($account) {
            $data['payment_apirone_mccp_account']  = serialize($account);
        }

        $this->model_setting_setting->editSetting('payment_apirone_mccp', $data);

        $query = ApironeApi\Db::createInvoicesTableQuery('oc_');
        $this->model_extension_payment_apirone_mccp->install_invoices_table($query);
    }

    public function uninstall() {
        $this->load->model('extension/payment/apirone_mccp');
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('payment_apirone_mccp');

        $query = ApironeApi\Db::deleteInvoicesTableQuery('oc_');
        $this->model_extension_payment_apirone_mccp->delete_invoices_table($query);
    }

}
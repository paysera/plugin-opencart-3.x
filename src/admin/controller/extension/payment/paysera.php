<?php
require_once(DIR_SYSTEM . 'library/WebToPay.php');

class ControllerExtensionPaymentPaysera extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/paysera');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (
            $this->request->server['REQUEST_METHOD'] == 'POST'
            && $this->validate()
        ) {
            $this->model_setting_setting->editSetting(
                'payment_paysera',
                $this->request->post
            );

            $this->session->data['success'] =
                $this->language->get('text_success');

            $this->response->redirect(
                $this->url->link(
                    'marketplace/extension',
                    'user_token='
                        . $this->session->data['user_token']
                        . '&type=payment',
                    true
                )
            );
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['project'])) {
            $data['error_project'] = $this->error['project'];
        } else {
            $data['error_project'] = '';
        }

        if (isset($this->error['sign'])) {
            $data['error_sign'] = $this->error['sign'];
        } else {
            $data['error_sign'] = '';
        }

        if (isset($this->error['lang'])) {
            $data['error_lang'] = $this->error['lang'];
        } else {
            $data['error_lang'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' =>
                $this->url->link(
                    'common/dashboard',
                    'user_token='
                        . $this->session->data['user_token'],
                    true
                )
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' =>
                $this->url->link(
                    'marketplace/extension',
                    'user_token='
                        . $this->session->data['user_token']
                        . '&type=payment',
                    true
                )
        );

        $data['breadcrumbs'][] =
            array(
                'text' => $this->language->get('heading_title'),
                'href' =>
                    $this->url->link(
                        'extension/payment/paysera',
                        'user_token='
                            . $this->session->data['user_token'],
                        true
                    )
            );

        $data['action'] =
            $this->url->link(
                'extension/payment/paysera',
                'user_token='
                    . $this->session->data['user_token'],
                true
            );

        $data['cancel'] =
            $this->url->link(
                'marketplace/extension',
                'user_token='
                    . $this->session->data['user_token']
                    . '&type=payment',
                true
            );

        if (isset($this->request->post['payment_paysera_project'])) {
            $data['payment_paysera_project'] =
                $this->request->post['payment_paysera_project'];
        } else {
            $data['payment_paysera_project'] =
                $this->config->get('payment_paysera_project');
        }

        if (isset($this->request->post['payment_paysera_sign'])) {
            $data['payment_paysera_sign'] =
                $this->request->post['payment_paysera_sign'];
        } else {
            $data['payment_paysera_sign'] =
                $this->config->get('payment_paysera_sign');
        }

        if (isset($this->request->post['payment_paysera_lang'])) {
            $data['payment_paysera_lang'] =
                $this->request->post['payment_paysera_lang'];
        } else {
            $data['payment_paysera_lang'] =
                $this->config->get('payment_paysera_lang');
        }

        $data['callback'] =
            HTTP_CATALOG . 'index.php?route=extension/payment/paysera/callback';

        if (isset($this->request->post['payment_paysera_total'])) {
            $data['payment_paysera_total'] =
                $this->request->post['payment_paysera_total'];
        } else {
            $data['payment_paysera_total'] =
                $this->config->get('payment_paysera_total');
        }

        if (isset($this->request->post['payment_paysera_test'])) {
            $data['payment_paysera_test'] =
                $this->request->post['payment_paysera_test'];
        } else {
            $data['payment_paysera_test'] =
                $this->config->get('payment_paysera_test');
        }

        if (isset($this->request->post['payment_paysera_order_status_id'])) {
            $data['payment_paysera_order_status_id'] =
                $this->request->post['payment_paysera_order_status_id'];
        } else {
            $data['payment_paysera_order_status_id'] =
                $this->config->get('payment_paysera_order_status_id');
        }

        if (isset($this->request->post['payment_paysera_canceled_order_status_id'])) {
            $data['payment_paysera_canceled_order_status_id'] =
                $this->request->post['payment_paysera_canceled_order_status_id'];
        } else {
            $data['payment_paysera_canceled_order_status_id'] =
                $this->config->get('payment_paysera_canceled_order_status_id');
        }

        if (isset($this->request->post['payment_paysera_new_order_status_id'])) {
            $data['payment_paysera_new_order_status_id'] =
                $this->request->post['payment_paysera_new_order_status_id'];
        } else {
            $data['payment_paysera_new_order_status_id'] =
                $this->config->get('payment_paysera_new_order_status_id');
        }

        if (isset($this->request->post['payment_paysera_display_payments_list'])) {
            $data['payment_paysera_display_payments_list'] =
                $this->request->post['payment_paysera_display_payments_list'];
        } else {
            $data['payment_paysera_display_payments_list'] =
                $this->config->get('payment_paysera_display_payments_list');
        }

        $data['paysera_countries'] = array();

        $methods =
            WebToPay::getPaymentMethodList(
                123,
                'EUR'
            )->filterForAmount(
                123,
                'EUR'
            )->setDefaultLanguage('en');
        $data['evp_countries'] = $methods->getCountries();

        foreach ($data['evp_countries'] as $country_id) {
            $data['paysera_countries'][$country_id->getCode()] =
                $country_id->getTitle();
        }

        if (isset($this->request->post['paysera_selected_country'])) {
            $data['payment_paysera_category'] =
                $this->request->post['payment_paysera_category'];
        } elseif ($this->config->get('payment_paysera_category')) {
            $data['payment_paysera_category'] =
                $this->config->get('payment_paysera_category');
        } else {
            $data['payment_paysera_category'] = array();
        }

        $data['paysera_selected_countries'] = array();

        foreach ($data['payment_paysera_category'] as $country => $id) {
            $data['paysera_selected_countries'][$id] =
                $data['paysera_countries'][$id];
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] =
            $this->model_localisation_order_status->getOrderStatuses();

        $data['payment_countries'] = $this->getCountries();

        if (isset($this->request->post['payment_paysera_geo_zone_id'])) {
            $data['payment_paysera_geo_zone_id'] =
                $this->request->post['payment_paysera_geo_zone_id'];
        } else {
            $data['payment_paysera_geo_zone_id'] =
                $this->config->get('payment_paysera_geo_zone_id');
        }

        if (isset($this->request->post['payment_paysera_default_country'])) {
            $data['payment_paysera_default_country'] =
                $this->request->post['payment_paysera_default_country'];
        } else {
            $data['payment_paysera_default_country'] =
                $this->config->get('payment_paysera_default_country');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] =
            $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_paysera_status'])) {
            $data['payment_paysera_status'] =
                $this->request->post['payment_paysera_status'];
        } else {
            $data['payment_paysera_status'] =
                $this->config->get('payment_paysera_status');
        }

        if (isset($this->request->post['payment_paysera_sort_order'])) {
            $data['payment_paysera_sort_order'] =
                $this->request->post['payment_paysera_sort_order'];
        } else {
            $data['payment_paysera_sort_order'] =
                $this->config->get('payment_paysera_sort_order');
        }

        if (isset($this->request->post['payment_paysera_grid_view'])) {
            $data['payment_paysera_grid_view'] =
                $this->request->post['payment_paysera_grid_view'];
        } else {
            $data['payment_paysera_grid_view'] =
                $this->config->get('payment_paysera_grid_view');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput(
            $this->load->view('extension/payment/paysera', $data)
        );
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/paysera')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_paysera_project']) {
            $this->error['project'] = $this->language->get('error_project');
        }

        if (!$this->request->post['payment_paysera_sign']) {
            $this->error['sign'] = $this->language->get('error_sign');
        }

        if (!$this->request->post['payment_paysera_lang']) {
            $this->error['lang'] = $this->language->get('error_lang');
        }

        return !$this->error;
    }

    private function getCountries()
    {
        $language = $this->language->get('code');
        $projectId = $this->config->get('payment_paysera_project');

        if (!$projectId || !$language) {
            return null;
        }

        $methods = WebToPay::getPaymentMethodList($projectId)
            ->setDefaultLanguage($language);

        return $methods->getCountries();
    }
}
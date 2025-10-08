<?php
class hcdigital_F24_Api {
    const API_BASE_URL = "https://api.data-crypt.com/api/v1.3";
    const TOKEN_URL = "https://identity.data-crypt.com/connect/token";

    private $access_token = null;

    public function __construct() {
        add_action('wp_ajax_hcdigital_get_f24_marketing_lists', [$this, 'ajax_get_marketing_lists']);
        add_action('wp_ajax_hcdigital_get_f24_contact_fields', [$this, 'ajax_get_contact_fields']);
    }

    private function get_access_token() {
        if ($this->access_token) {
            return $this->access_token;
        }

        $client_id = get_option('force24_api_key', '');
        $client_secret = get_option('force24_api_secret', '');

        if (empty($client_id) || empty($client_secret)) {
            return new WP_Error('f24_api_error', 'Force24 API credentials are not set.');
        }

        $response = wp_remote_post(self::TOKEN_URL, [
            'body' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
            ],
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $token_data = json_decode($body, true);

        if (isset($token_data['access_token'])) {
            $this->access_token = $token_data['access_token'];
            set_transient('f24_access_token', $this->access_token, $token_data['expires_in'] - 60);
            return $this->access_token;
        }

        return new WP_Error('f24_api_error', 'Failed to get Force24 access token.', $token_data);
    }

    public function get_endpoint($endpoint) {
        $access_token = get_transient('f24_access_token');
        if (false === $access_token) {
            $access_token = $this->get_access_token();
        }

        if (is_wp_error($access_token)) {
            return $access_token;
        }
        $api_url = self::API_BASE_URL . $endpoint;
        $response = wp_remote_get($api_url, [
            'headers' => [
                "Authorization" => "Bearer $access_token",
                "Content-Type"  => "application/json",
            ],
        ]);
        if (is_wp_error($response)) {
            return $response;
        }
        return wp_remote_retrieve_body($response);
    }

    public function post_to_endpoint($endpoint, $data = [] ) {
        $access_token = get_transient('f24_access_token');
        if (false === $access_token) {
            $access_token = $this->get_access_token();
        }

        if (is_wp_error($access_token)) {
            return $access_token;
        }

        $api_url = self::API_BASE_URL . $endpoint;

        $response = wp_remote_post($api_url, [
            'headers' => [
                "Authorization" => "Bearer $access_token",
                "Content-Type"  => "application/json",
            ],
            'body' => json_encode($data),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        

        // if ($action_hook_name) {
        //     do_action($action_hook_name, json_decode($response_body, true), $endpoint, $data);
        // }

        return wp_remote_retrieve_body($response);
    }

    public function ajax_get_marketing_lists() {
        $result = $this->get_endpoint('/marketing-lists?take=500');
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success(json_decode($result, true));
    }

    public function ajax_get_contact_fields() {
        $result = $this->get_endpoint('/schemas/contact/fields');
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success(json_decode($result, true));
    }
}

new hcdigital_F24_Api();
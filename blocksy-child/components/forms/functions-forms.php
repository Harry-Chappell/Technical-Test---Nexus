<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/admin/functions-forms-admin.php';
require_once __DIR__ . '/api/functions-f24api.php';
class hcdigital_Form {
    public function __construct() {
        add_action('init', [$this, 'register_form_blocks']);
        add_action('wp_ajax_nopriv_hcdigital_form_submission', [$this, 'handle_form_submission']);
        add_action('wp_ajax_hcdigital_form_submission', [$this, 'handle_form_submission']);
        add_action('wp_footer', [$this, 'add_form_styles']);
        add_action('wp_ajax_hcdigital_get_f24_integration_status', function() {
        wp_send_json_success([
            'enabled' => get_option('force24_integration_enabled', false)
        ]);
    });
    }

    public function register_form_blocks() {
        $dir_path = __DIR__;
        $relative_path = str_replace(get_theme_file_path(), '', $dir_path);
        $dir_url = get_theme_file_uri($relative_path);

        wp_register_script(
            'hcdigital-form-block-editor',
            $dir_url . '/block/block-form-editor.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data'],
            filemtime($dir_path . '/block/block-form-editor.js')
        );

        wp_register_script(
            'hcdigital-form-frontend',
            $dir_url . '/block/block-form-frontend.js',
            [],
            filemtime($dir_path . '/block/block-form-frontend.js'),
            true
        );

        register_block_type('hcdigital/form', [
            'editor_script' => 'hcdigital-form-block-editor',
            'render_callback' => [$this, 'render_form_block'],
            'attributes' => [
                'formName' => ['type' => 'string', 'default' => ''],
                'recipientEmail' => ['type' => 'string', 'default' => ''],
                'emailSubject' => ['type' => 'string', 'default' => 'New Form Submission'],
                'submitButtonText' => ['type' => 'string', 'default' => 'Submit'],
                'thankYouPageId' => ['type' => 'number', 'default' => 0],
                'thankYouMessage' => ['type' => 'string', 'default' => 'Thank you for your submission!'],
                'formId' => ['type' => 'string', 'default' => ''],
                'showOptinCheckbox' => ['type' => 'boolean', 'default' => false],
                'optinCheckboxLabel' => ['type' => 'string', 'default' => 'Opt in to our newsletter'],
                'isOptinRequired' => ['type' => 'boolean', 'default' => false],
                'enableApiIntegration' => ['type' => 'boolean', 'default' => false],
                'apiMarketingListId' => ['type' => 'string', 'default' => ''],
            ]
        ]);

        register_block_type('hcdigital/form-input', ['editor_script' => 'hcdigital-form-block-editor']);
        register_block_type('hcdigital/form-textarea', ['editor_script' => 'hcdigital-form-block-editor']);
        register_block_type('hcdigital/form-select', ['editor_script' => 'hcdigital-form-block-editor']);
    }

    public function render_form_block($attributes, $content) {
        wp_enqueue_script('hcdigital-form-frontend');
        wp_localize_script('hcdigital-form-frontend', 'hcdigital_form_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);

        $form_id = isset($attributes['formId']) ? $attributes['formId'] : '';
        $recipient_email = isset($attributes['recipientEmail']) ? $attributes['recipientEmail'] : '';

        if ($form_id && $recipient_email) {
            if (false === get_transient('hcdigital_form_recipient_' . $form_id)) {
                set_transient('hcdigital_form_recipient_' . $form_id, $recipient_email, DAY_IN_SECONDS);
            }
        }

        $form_html = '<form class="hcdigital-form" method="post" enctype="multipart/form-data">';
        $form_html .= '<div style="position: absolute; left: -5000px;" aria-hidden="true">';
        $form_html .= '<input type="text" name="honeypot" tabindex="-1" value="" autocomplete="new-password">';


        if (!empty($attributes['enableApiIntegration']) && !empty($attributes['apiMarketingListId'])) {
            $form_html .= '<input type="hidden" name="f24_mapping[MarketingListId]" value="' . esc_html($attributes['apiMarketingListId']) . '">';
        }
        $form_html .= '</div>';
        $form_html .= $content;

        if (!empty($attributes['showOptinCheckbox'])) {
            $label = $attributes['optinCheckboxLabel'];
            $is_required = !empty($attributes['isOptinRequired']);
            $required_attr = $is_required ? ' required' : '';
            $optin_id = 'f24_optin_' . esc_attr($form_id);

            $form_html .= '<div class="form-field form-optin-checkbox">';
            $form_html .= '<label for="' . $optin_id . '"><input id="' . $optin_id . '" type="checkbox" name="f24_mapping[optin]" value="1"' . $required_attr . '> ' . esc_html($label) . '</label>';
            $form_html .= '</div>';
        }
        
        $form_html .= '<div class="form-submit">';
        $form_html .= '<input type="hidden" name="action" value="hcdigital_form_submission">';
        $form_html .= '<input type="hidden" name="form_id" value="' . esc_attr($form_id) . '">';
        $form_html .= '<input type="hidden" name="form_name" value="' . esc_attr($attributes['formName']) . '">';
        $form_html .= '<input type="hidden" name="email_subject" value="' . esc_attr($attributes['emailSubject']) . '">';
        $form_html .= '<input type="hidden" name="thank_you_message" value="' . esc_attr($attributes['thankYouMessage']) . '">';
        if (!empty($attributes['thankYouPageId'])) {
            $form_html .= '<input type="hidden" name="thank_you_page_id" value="' . esc_attr($attributes['thankYouPageId']) . '">';
        }
        $form_html .= '<input type="hidden" name="form_timestamp" value="' . time() . '">';
        $form_html .= '<input type="hidden" name="js_check" class="js-check-field" value="">';
        $form_html .= wp_nonce_field('hcdigital_form_action', 'hcdigital_form_nonce', true, false);
        $form_html .= '<br><button type="submit" class="wp-block-button__link">' . esc_html($attributes['submitButtonText']) . '</button>';
        $form_html .= '</div>';
        $form_html .= '</form>';

        return $form_html;
    }

    public function handle_form_submission() {
        if (isset($_POST['honeypot']) && !empty($_POST['honeypot'])) {
            wp_send_json_error('An error occurred. Please try again.', 400);
        }

        if (isset($_POST['form_timestamp']) && (time() - (int)$_POST['form_timestamp']) < 2) {
            wp_send_json_error('Trying to submit too quickly. Please try again.', 400);
        }

        if (!isset($_POST['js_check']) || $_POST['js_check'] !== 'valid') {
            wp_send_json_error('An error occurred. Please try again.', 400);
        }

        if (!isset($_POST['hcdigital_form_nonce']) || !wp_verify_nonce($_POST['hcdigital_form_nonce'], 'hcdigital_form_action')) {
            wp_send_json_error('Sorry, your session has expired. Please reload the page and try again.', 403);
        }

        $form_id = isset($_POST['form_id']) ? sanitize_text_field($_POST['form_id']) : '';
        $thank_you_message = isset($_POST['thank_you_message']) ? sanitize_text_field($_POST['thank_you_message']) : 'Form submitted successfully!';
        $thank_you_page_id = isset($_POST['thank_you_page_id']) ? absint($_POST['thank_you_page_id']) : 0;

        $recipient_emails = $form_id ? get_transient('hcdigital_form_recipient_' . $form_id) : '';

        if (empty($recipient_emails)) {
            wp_send_json_error('Could not send email. The form configuration is incomplete or has expired.', 400);
        }

        $subject = sanitize_text_field($_POST['email_subject']);
        $form_name = sanitize_text_field($_POST['form_name']);

        $exclude_keys = ['action', 'recipient_email', 'email_subject', 'hcdigital_form_nonce', '_wp_http_referer', 'thank_you_page_id', 'form_id', 'thank_you_message', 'form_name', 'honeypot', 'form_timestamp', 'js_check', 'f24_mapping'];

        $reply_to_email = '';

        foreach ($_POST as $key => $value) {
            if (str_ends_with($key, '_is_reply_to') && $value === 'true') {
                $field_name = str_replace('_is_reply_to', '', $key);
                if (isset($_POST[$field_name])) {
                    $sanitized_val = sanitize_email($_POST[$field_name]);
                    if (is_email($sanitized_val)) {
                        $reply_to_email = $sanitized_val;
                        break;
                    }
                }
            }
        }

        $uploaded_files = [];
        if (!empty($_FILES)) {
            $target_dir = JSON_PATH . '/form-entries/form-uploads';
            $target_url = JSON_URL . '/form-entries/form-uploads';
            if (!is_dir($target_dir)) {
                wp_mkdir_p($target_dir);
            }
            foreach ($_FILES as $key => $file) {
                $allowed_mimes = apply_filters('hcdigital_form_allowed_mime_types', [
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    'png'          => 'image/png',
                ]);

                $max_size = 5 * 1024 * 1024; // 5MB

                if ($file['error'] === UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])) {
                    $file_info = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mimes);

                    if (empty($file_info['ext']) || empty($file_info['type'])) {
                        wp_send_json_error('File type not allowed to upload', 400);
                    }

                    if ($file['size'] > $max_size) {
                        wp_send_json_error('File too large, max size is 5MB', 400);
                    }

                    $filename = uniqid() . '-' . sanitize_file_name($file['name']);
                    $target_file = $target_dir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        $file_url = $target_url . '/' . $filename;
                        $uploaded_files[$key] = $file_url;
                    }
                }
            }
        }

        $submission_date = current_time('mysql');
        $body = "New submission from form: " . esc_html($form_name) . "\n\n";
        $entry_data = [
            'form_name' => $form_name,
            'submission_date' => $submission_date,
            'submission_ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        ];

        foreach ($_POST as $key => $value) {
            if (in_array($key, $exclude_keys) || str_ends_with($key, '_is_reply_to')) continue;
            if (isset($_FILES[$key])) continue;
            if (is_array($value)) {
                $sanitized_value = array_map('sanitize_textarea_field', $value);
                $body .= esc_html(ucwords(str_replace(['-', '_'], ' ', $key))) . ":\n" . implode(', ', $sanitized_value) . "\n\n";
                $entry_data[$key] = $sanitized_value;
            } else {
                $sanitized_value = sanitize_textarea_field(stripslashes($value));
                $body .= esc_html(ucwords(str_replace(['-', '_'], ' ', $key))) . ":\n" . esc_html($sanitized_value) . "\n\n";
                $entry_data[$key] = $sanitized_value;
            }
        }

        if (!empty($uploaded_files)) {
            $body .= "\nUploaded Files:\n";
            foreach ($uploaded_files as $field => $url) {
                $body .= esc_html($field) . ': ' . esc_url($url) . "\n";
                $entry_data[$field] = $url;
            }
        }

        $entries_dir = JSON_PATH . '/form-entries';
        if (!is_dir($entries_dir)) {
            wp_mkdir_p($entries_dir);
        }
        $email_for_filename = !empty($reply_to_email) ? $reply_to_email : 'invalid-email';

        $filename = $form_name . '-' . sanitize_file_name($email_for_filename . '-' . $submission_date . '.json');
        $filepath = $entries_dir . '/' . $filename;

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        $wp_filesystem->put_contents($filepath, wp_json_encode($entry_data, JSON_PRETTY_PRINT), FS_CHMOD_FILE);

        
        
        $f24_data = $this->_prepare_f24_api_data($_POST);
  
        if ($f24_data) {
            $this->_handle_f24_submission($f24_data);
        }

        $headers = [];
        if (!empty($reply_to_email)) {
            $headers[] = 'Reply-To: ' . $reply_to_email;
        }
        $mail_sent = wp_mail($recipient_emails, $subject, $body, $headers);
   
        $response_data = ['message' => $thank_you_message];
        if ($thank_you_page_id > 0 && ($redirect_url = get_permalink($thank_you_page_id))) {
            $response_data['redirect'] = $redirect_url;
        }
        wp_send_json_success($response_data);
       
    }

    private function _handle_f24_submission($f24_data) {

        if (!$f24_data) {
            return;
        }        

        $f24_api = new hcdigital_F24_Api();
        $upsert_payload = [
            'items' => [
                [
                    'emailaddress' => $f24_data['email'],
                    'fields' => $f24_data['user_data']
                ]
            ]
        ];

        $upsert_response = $f24_api->post_to_endpoint("/contacts/upsert", $upsert_payload);
        $upsert_data = json_decode($upsert_response, true);

        if (
            !empty($upsert_data['items'][0]['id']) &&
            isset($upsert_data['items'][0]['status']) &&
            $upsert_data['items'][0]['status'] === 'Success'
        ) {
            $contact_id = $upsert_data['items'][0]['id'];            
            $f24_api->post_to_endpoint("/marketing-lists/{$f24_data['marketing_list_id']}/contacts/{$contact_id}/add");

        }
    }

    private function _prepare_f24_api_data($post_data) {
        if (!isset($post_data['f24_mapping']['optin']) || empty($post_data['f24_mapping']['MarketingListId']) || empty($post_data['f24_mapping']['fields'])) {
            return null;
        }

        $user_data = [];
        $contact_email = '';
        $api_marketing_list_id = sanitize_text_field($post_data['f24_mapping']['MarketingListId']);
        $fields_map = $post_data['f24_mapping']['fields'];

        foreach ($fields_map as $form_field_name => $api_field_name) {
            if (isset($post_data[$form_field_name])) {
                $submitted_value = $post_data[$form_field_name];
                $sanitized_value = '';

                if (is_array($submitted_value)) {
                    $sanitized_value = implode(',', array_unique(array_map('sanitize_text_field', $submitted_value)));
                } else {
                    $sanitized_value = sanitize_text_field($submitted_value);
                }

                if (strtolower($api_field_name) === 'emailaddress') {
                    $contact_email = $sanitized_value;
                } else {
                    $user_data[$api_field_name] = $sanitized_value;
                }
            }
        }
        
        if (empty($contact_email)) {
            return null;
        }

        return [
            'marketing_list_id' => $api_marketing_list_id,
            'email' => $contact_email,
            'user_data' => $user_data,
        ];
    }

    public function add_form_styles() {
        echo '<style>
        .hcdigital-form label { display: block; }
        .notice {
            padding: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #0073aa;
            background-color: #98dfffff;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .notice-error {
            background-color: #ffccccff;
            border-left-color: #d63638;
        }
        .notice-success {
            background-color: #bbffbbff;
            border-left-color: #46b450;
        }
        </style>';
    }
}

new hcdigital_Form();
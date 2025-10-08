<?php
if (!defined('ABSPATH')) {
    exit;
}

class hcdigital_Form_Settings {

    private $option_group = 'hcdigital_form_settings_group';

    private $page_slug = 'hcdigital-form-settings';

    public function __construct() {
        add_action('admin_init', [$this, 'settings_init']);
    }

    public function settings_init() {
        register_setting(
            $this->option_group,
            'force24_integration_enabled',
            [
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
                'show_in_rest' => false,
            ]
        );

        register_setting(
            $this->option_group,
            'force24_api_key',
            [
                'type' => 'string',
                'sanitize_callback' => [$this, 'sanitize_api_key'],
                'default' => '',
                'show_in_rest' => false,
            ]
        );

        register_setting(
            $this->option_group,
            'force24_api_secret',
            [
                'type' => 'string',
                'sanitize_callback' => [$this, 'sanitize_api_secret'],
                'default' => '',
                'show_in_rest' => false,
            ]
        );

        add_settings_section(
            'hcdigital_force24_section',
            'Force24 Integration Settings',
            [$this, 'section_callback'],
            $this->page_slug
        );

        add_settings_field(
            'force24_integration_enabled_field',
            'Enable Force24 Integration',
            [$this, 'integration_enabled_callback'],
            $this->page_slug,
            'hcdigital_force24_section'
        );

        add_settings_field(
            'force24_api_key_field',
            'API Key',
            [$this, 'api_key_callback'],
            $this->page_slug,
            'hcdigital_force24_section'
        );

        add_settings_field(
            'force24_api_secret_field',
            'API Secret',
            [$this, 'api_secret_callback'],
            $this->page_slug,
            'hcdigital_force24_section'
        );
    }

    public function sanitize_api_key($value) {
        $integration_enabled = !empty($_POST['force24_integration_enabled']);
        if ($integration_enabled && empty($value)) {
            add_settings_error('force24_api_key', 'force24_api_key_required', 'API Key is required when Force24 Integration is enabled.', 'error');
            return get_option('force24_api_key', '');
        }
        return sanitize_text_field($value);
    }

    public function sanitize_api_secret($value) {
        $integration_enabled = !empty($_POST['force24_integration_enabled']);
        if ($integration_enabled && empty($value)) {
            add_settings_error('force24_api_secret', 'force24_api_secret_required', 'API Secret is required when Force24 Integration is enabled.', 'error');
            return get_option('force24_api_secret', '');
        }
        return sanitize_text_field($value);
    }

    public function section_callback() {
        echo '<p>Configure your Force24 integration settings. If enabled, form submissions can be sent to Force24.</p>';
    }

    public function render_settings_page() {
        echo '<div class="wrap"><h1>Forms Settings</h1>';
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        settings_errors();

        echo '<form method="post" action="options.php">';
        settings_fields($this->option_group);
        do_settings_sections($this->page_slug);
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function integration_enabled_callback() {
        $enabled = get_option('force24_integration_enabled', false);
        echo '<label class="toggle-switch" for="force24_integration_enabled">';
        echo '<input type="checkbox" id="force24_integration_enabled" name="force24_integration_enabled" value="1"' . checked(1, $enabled, false) . '>';
        echo '<span class="slider"></span>';
        echo '</label>';

        echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                const toggle = document.getElementById("force24_integration_enabled");
                const apiKeyFieldRow = document.getElementById("force24_api_key_field").closest("tr");
                const apiSecretFieldRow = document.getElementById("force24_api_secret_field").closest("tr");

                function toggleFieldsVisibility() {
                    if (toggle.checked) {
                        apiKeyFieldRow.style.display = "table-row";
                        apiSecretFieldRow.style.display = "table-row";
                    } else {
                        apiKeyFieldRow.style.display = "none";
                        apiSecretFieldRow.style.display = "none";
                    }
                }

                toggle.addEventListener("change", toggleFieldsVisibility);
                toggleFieldsVisibility();
            });
        </script>';
    }

    public function api_key_callback() {
        $api_key = get_option('force24_api_key', '');
        echo '<input autocomplete="new-password" type="text" id="force24_api_key_field" name="force24_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
    }

    public function api_secret_callback() {
        $api_secret = get_option('force24_api_secret', '');
        echo '<input autocomplete="new-password" type="password" id="force24_api_secret_field" name="force24_api_secret" value="' . esc_attr($api_secret) . '" class="regular-text">';
    }
}

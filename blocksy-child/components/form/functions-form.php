<?php
if (!defined('ABSPATH')) {
    exit;
}


function hcdigital_register_form_blocks() {
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
        'render_callback' => 'hcdigital_render_form_block',
        'attributes' => [
            'formName' => ['type' => 'string', 'default' => ''],
            'recipientEmail' => ['type' => 'string', 'default' => ''],
            'emailSubject' => ['type' => 'string', 'default' => 'New Form Submission'],
            'submitButtonText' => ['type' => 'string', 'default' => 'Submit'],
            'thankYouPageId' => ['type' => 'number', 'default' => 0],
            'thankYouMessage' => ['type' => 'string', 'default' => 'Thank you for your submission!'],
            'formId' => ['type' => 'string', 'default' => ''],
        ]
    ]);

    register_block_type('hcdigital/form-input', ['editor_script' => 'hcdigital-form-block-editor']);
    register_block_type('hcdigital/form-textarea', ['editor_script' => 'hcdigital-form-block-editor']);
    register_block_type('hcdigital/form-select', ['editor_script' => 'hcdigital-form-block-editor']);
}
add_action('init', 'hcdigital_register_form_blocks');

function hcdigital_render_form_block($attributes, $content) {
    wp_enqueue_script('hcdigital-form-frontend');
    wp_localize_script('hcdigital-form-frontend', 'hcdigital_form_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);

    $form_id = isset($attributes['formId']) ? $attributes['formId'] : '';
    $recipient_email = isset($attributes['recipientEmail']) ? $attributes['recipientEmail'] : '';

    if ($form_id && $recipient_email) {
        
        if (false === get_transient('hcdigital_form_recipient_' . $form_id)) {
            set_transient('hcdigital_form_recipient_' . $form_id, $recipient_email, DAY_IN_SECONDS);
        }
    }
    
    $form_html = '<form class="hcdigital-form" method="post">';
    $form_html .= '<div style="position: absolute; left: -5000px;" aria-hidden="true">';
    $form_html .= '<input type="text" name="user_website" tabindex="-1" value=""></div>';
    $form_html .= $content;
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

function handle_hcdigital_form_submission() {
    
    if (isset($_POST['user_website']) && !empty($_POST['user_website'])) {
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
    $recipient_emails = $form_id ? get_transient('hcdigital_form_recipient_' . $form_id) : '';

    if (empty($recipient_emails)) {
        wp_send_json_error('Could not send email. The form configuration is incomplete or has expired.', 400);
    }
    

    $subject = sanitize_text_field($_POST['email_subject']);
    $form_name = sanitize_text_field($_POST['form_name']);
    
    $exclude_keys = ['action', 'recipient_email', 'email_subject', 'hcdigital_form_nonce', '_wp_http_referer', 'thank_you_page_id', 'form_id', 'thank_you_message', 'form_name', 'user_website', 'form_timestamp', 'js_check'];
    
    
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

    $submission_date = current_time('mysql');
    $body = "New submission from form: " . esc_html($form_name) . "\n\n";
    $entry_data = [
        'form_name' => $form_name,
        'submission_date' => $submission_date,
        'submission_ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
    ];

    foreach ($_POST as $key => $value) {
        if (in_array($key, $exclude_keys) || str_ends_with($key, '_is_reply_to')) {
            continue;
        }
        $label = ucwords(str_replace(['-', '_'], ' ', $key));
        $sanitized_value = is_array($value) ? array_map('sanitize_textarea_field', $value) : sanitize_textarea_field(stripslashes($value));
        $body .= esc_html($label) . ":\n" . (is_array($sanitized_value) ? implode(', ', $sanitized_value) : esc_html($sanitized_value)) . "\n\n";
        $entry_data[$key] = $sanitized_value;
    }

    
    $entries_dir = __DIR__ . '/entries';
    if (!is_dir($entries_dir)) {
        wp_mkdir_p($entries_dir);
    }
    $email_for_filename = !empty($reply_to_email) ? $reply_to_email : 'invalid-email';
    
    $filename = $form_name . '-' . sanitize_file_name( $email_for_filename . '-' . $submission_date . '.json');
    $filepath = $entries_dir . '/' . $filename;

    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once (ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }
    $wp_filesystem->put_contents($filepath, wp_json_encode($entry_data, JSON_PRETTY_PRINT), FS_CHMOD_FILE);

    if (!empty($reply_to_email)) {
        $headers[] = 'Reply-To: <' . $reply_to_email . '>';
    }

    wp_mail($recipient_emails, $subject, $body, $headers);

    if (!empty($_POST['thank_you_page_id'])) {
        $thank_you_page_id = absint($_POST['thank_you_page_id']);
        $permalink = get_permalink($thank_you_page_id);
        if ($permalink) {
            wp_send_json_success(['redirect' => $permalink]);
        }
    }

    $message = !empty($_POST['thank_you_message']) ? esc_html($_POST['thank_you_message']) : 'Thank you for your submission!';
    wp_send_json_success(['message' => $message ]);
}
add_action('wp_ajax_nopriv_hcdigital_form_submission', 'handle_hcdigital_form_submission');
add_action('wp_ajax_hcdigital_form_submission', 'handle_hcdigital_form_submission');


add_action('admin_menu', function() {
    add_menu_page(
        'Form Entries',
        'Form Entries',
        'manage_options',
        'hcdigital-form-entries',
        'hcdigital_form_entries_page',
        'dashicons-media-document',
        26
    );
});


function hcdigital_form_entries_page() {
    $entries_dir = __DIR__ . '/entries';
    echo '<div class="wrap"><h1>Form Entries</h1>';

    if (!is_dir($entries_dir)) {
        echo '<p>No entries directory found.</p></div>';
        return;
    }

    $files = glob($entries_dir . '/*.json');
    if (!$files) {
        echo '<p>No entries found.</p></div>';
        return;
    }

    $form_names = [];
    $dates = [];
    $entries = [];

    foreach ($files as $file) {
        $basename = basename($file);
        $parts = explode('-', $basename);
        $form_name = $parts[0] ?? '';
        $content = @file_get_contents($file);
        $json = json_decode($content, true);
        $date = $json['submission_date'] ?? '';
        $month = $date ? substr($date, 0, 7) : '';
        if ($form_name) $form_names[] = $form_name;
        if ($month) $dates[] = $month;
        $entries[] = [
            'file' => $file,
            'basename' => $basename,
            'form_name' => $form_name,
            'date' => $date,
            'month' => $month,
        ];
    }
    $form_names = array_unique($form_names);
    $dates = array_unique($dates);
    sort($form_names);
    sort($dates);

    $selected_form = isset($_GET['form']) ? sanitize_text_field($_GET['form']) : '';
    $selected_month = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';

    // Filter entries based on selection
    $filtered_entries = $entries;
    if ($selected_form) {
        $filtered_entries = array_filter($filtered_entries, function($entry) use ($selected_form) {
            return $entry['form_name'] === $selected_form;
        });
    }
    if ($selected_month) {
        $filtered_entries = array_filter($filtered_entries, function($entry) use ($selected_month) {
            return $entry['month'] === $selected_month;
        });
    }

    // Get filtered forms and months for dropdowns
    $filtered_forms = array_unique(array_column($filtered_entries, 'form_name'));
    $filtered_months = array_unique(array_column($filtered_entries, 'month'));
    sort($filtered_forms);
    sort($filtered_months);

    // Sort by date descending
    usort($filtered_entries, function($a, $b) {
        return strtotime($b['date']) <=> strtotime($a['date']);
    });

    // CSV download handler
    if (isset($_GET['download_csv']) && $_GET['download_csv'] === '1') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="form-entries.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Entry', 'Date', 'Form Name']);
        foreach ($filtered_entries as $entry) {
            fputcsv($output, [$entry['basename'], $entry['date'], $entry['form_name']]);
        }
        fclose($output);
        exit;
    }

    echo '<form method="get" style="margin-bottom:20px;">';
    echo '<input type="hidden" name="page" value="hcdigital-form-entries">';
    echo '<label for="form-filter">Choose form: </label>';
    echo '<select name="form" id="form-filter" onchange="this.form.submit()">';
    echo '<option value="">-- Select --</option>';
    foreach ($filtered_forms as $form_name) {
        echo '<option value="' . esc_attr($form_name) . '" ' . selected($selected_form, $form_name, false) . '>' . esc_html($form_name) . '</option>';
    }
    echo '</select>';

    echo ' <label for="date-filter">Month: </label>';
    echo '<select name="date" id="date-filter" onchange="this.form.submit()">';
    echo '<option value="">-- All Months --</option>';
    foreach ($filtered_months as $date) {
        $display_month = date('F Y', strtotime($date . '-01'));
        echo '<option value="' . esc_attr($date) . '" ' . selected($selected_month, $date, false) . '>' . esc_html($display_month) . '</option>';
    }
    echo '</select>';

    $query_args = [
        'page' => 'hcdigital-form-entries',
        'form' => $selected_form,
        'date' => $selected_month,
        'download_csv' => '1'
    ];
    $csv_url = admin_url('admin.php?' . http_build_query($query_args));
    echo ' <a href="' . esc_url($csv_url) . '" class="button" style="margin-left:10px;">Download CSV</a>';

    echo '</form>';

    if (!$filtered_entries) {
        echo '<p>No entries found for this filter.</p></div>';
        return;
    }

    echo '<table class="widefat"><thead><tr><th>Entry</th><th>Form</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
    foreach ($filtered_entries as $entry) {
        $view_url = admin_url('admin.php?page=hcdigital-form-entries&form=' . urlencode($selected_form) . '&date=' . urlencode($selected_month) . '&view=' . urlencode($entry['basename']));
        echo '<tr>';
        echo '<td>' . esc_html($entry['basename']) . '</td>';
        echo '<td>' . esc_html($entry['form_name']) . '</td>';
        echo '<td>' . esc_html($entry['date']) . '</td>';
        echo '<td><a href="' . esc_url($view_url) . '">View</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    if (isset($_GET['view'])) {
        $view_file = $entries_dir . '/' . basename($_GET['view']);
        if (is_readable($view_file)) {
            $content = file_get_contents($view_file);
            echo '<h2>Entry: ' . esc_html($_GET['view']) . '</h2>';
            echo '<pre>' . esc_html($content) . '</pre>';
        }
    }

    echo '</div>';
}
<?php
if (!defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/functions-forms-admin-settings.php';

class hcdigital_Forms_Admin {
    private $settings_page;

    public function __construct() {
        $this->settings_page = new hcdigital_Form_Settings();
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_hcdigital_export_csv', [$this, 'export_csv']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Forms',
            'Forms',
            'manage_options',
            'hcdigital-form-entries',
            [$this, 'render_entries_page'],
            'dashicons-media-document',
            26
        );
        add_submenu_page(
            'hcdigital-form-entries',
            'Settings',
            'Settings',
            'manage_options',
            'hcdigital-form-settings',
            [$this->settings_page, 'render_settings_page']
        );
    }

    private function render_pagination($page, $total_pages, $total, $current_url) {
        $first_page_url = add_query_arg('paged', 1, $current_url);
        $prev_page_url = add_query_arg('paged', max(1, $page - 1), $current_url);
        $next_page_url = add_query_arg('paged', min($total_pages, $page + 1), $current_url);
        $last_page_url = add_query_arg('paged', $total_pages, $current_url);

        echo '<div class="tablenav-pages">
            <span class="displaying-num">' . $total . ' items</span>
            <span class="pagination-links">';
        if ($page > 1) {
            echo '<a class="first-page button" style="margin-right:2px;" href="' . esc_url($first_page_url) . '"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a> ';
            echo '<a class="prev-page button" style="margin-right:4px;" href="' . esc_url($prev_page_url) . '"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a> ';
        } else {
            echo '<span class="tablenav-pages-navspan button disabled" style="margin-right:2px;" aria-hidden="true">«</span> ';
            echo '<span class="tablenav-pages-navspan button disabled" style="margin-right:4px;" aria-hidden="true">‹</span> ';
        }
        echo '<span class="screen-reader-text">Current Page</span>
            <span id="table-paging" class="paging-input" style="margin-right:4px;">
                <span class="tablenav-paging-text">' . $page . ' of <span class="total-pages">' . $total_pages . '</span></span>
            </span>';
        if ($page < $total_pages) {
            echo '<a class="next-page button" style="margin-right:2px;" href="' . esc_url($next_page_url) . '"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a> ';
            echo '<a class="last-page button" href="' . esc_url($last_page_url) . '"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>';
        } else {
            echo '<span class="tablenav-pages-navspan button disabled" style="margin-right:2px;" aria-hidden="true">›</span> ';
            echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>';
        }
        echo '</span></div>';
    }

    private function render_filter_form($form_names, $months, $selected_form, $selected_month) {
        $filter_form = '<form method="get" style="margin-bottom:0;">';
        $filter_form .= '<input type="hidden" name="page" value="hcdigital-form-entries">';
        $filter_form .= '<select name="form_name" id="hcdigital-form-name-select"><option value="">All Forms</option>';
        foreach ($form_names as $name) {
            $selected = ($selected_form === $name) ? ' selected' : '';
            $filter_form .= '<option value="' . esc_attr($name) . '"' . $selected . '>' . esc_html($name) . '</option>';
        }
        $filter_form .= '</select> ';
        if ($selected_form && !empty($months)) {
            $filter_form .= '<select name="month_year" id="hcdigital-month-year-select"><option value="">All Dates</option>';
            foreach ($months as $value => $label) {
                $selected = ($selected_month === $value) ? ' selected' : '';
                $filter_form .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
            }
            $filter_form .= '</select> ';
        }
        $filter_form .= '<input type="submit" class="button" value="Filter">';
        $filter_form .= '</form>';
        $filter_form .= '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var formSelect = document.getElementById("hcdigital-form-name-select");
            var monthSelect = document.getElementById("hcdigital-month-year-select");
            if(formSelect && monthSelect) {
                formSelect.addEventListener("change", function() {
                    if(monthSelect) monthSelect.selectedIndex = 0;
                });
            }
        });
        </script>
        ';
        return $filter_form;
    }

    private function get_form_entries($filters = []) {
        static $all_entries = null;

        if ($all_entries === null) {
            $entries_dir = JSON_PATH . '/form-entries';
            $files = glob($entries_dir . '/*.json');
            $entries = [];
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if ($content) {
                    $entry = json_decode($content, true);
                    if (is_array($entry)) {
                        $entry['__file'] = $file;
                        $entry['__timestamp'] = isset($entry['submission_date']) ? strtotime($entry['submission_date']) : 0;
                        $entries[] = $entry;
                    }
                }
            }

            usort($entries, function($a, $b) {
                return $b['__timestamp'] - $a['__timestamp'];
            });
            $all_entries = $entries;
        }

        if (empty($filters)) {
            return $all_entries;
        }

        $filtered_entries = array_filter($all_entries, function($entry) use ($filters) {
            if (!empty($filters['form_name']) && (!isset($entry['form_name']) || $entry['form_name'] !== $filters['form_name'])) {
                return false;
            }
            if (!empty($filters['month_year']) && (!isset($entry['__timestamp']) || date('Y-m', $entry['__timestamp']) !== $filters['month_year'])) {
                return false;
            }
            return true;
        });

        return array_values($filtered_entries);
    }

    public function render_entries_page() {
        $all_entries = $this->get_form_entries();

        $selected_form = isset($_GET['form_name']) ? sanitize_text_field($_GET['form_name']) : '';
        $selected_month = isset($_GET['month_year']) ? sanitize_text_field($_GET['month_year']) : '';

        $form_names = array_values(array_unique(array_column($all_entries, 'form_name')));
        sort($form_names);

        $months = [];
        if ($selected_form) {
            $form_entries = array_filter($all_entries, function($entry) use ($selected_form) {
                return isset($entry['form_name']) && $entry['form_name'] === $selected_form;
            });
            foreach ($form_entries as $entry) {
                if (!empty($entry['submission_date'])) {
                    $ts = $entry['__timestamp'];
                    if ($ts) {
                        $month_year = date('Y-m', $ts);
                        $months[$month_year] = date('F Y', $ts);
                    }
                }
            }
            krsort($months);
        }
        krsort($months);

        $filtered_entries = $this->get_form_entries([
            'form_name' => $selected_form,
            'month_year' => $selected_month,
        ]);

        $per_page = 10;
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $total = count($filtered_entries);
        $total_pages = max(1, ceil($total / $per_page));
        $offset = ($page - 1) * $per_page;
        $entries_page = array_slice($filtered_entries, $offset, $per_page);

        $base_url = remove_query_arg(['paged', 'entry']);
        $current_url = $base_url;
        if ($selected_form) $current_url = add_query_arg('form_name', $selected_form, $current_url);
        if ($selected_form && !empty($selected_month)) $current_url = add_query_arg('month_year', $selected_month, $current_url);

        

        echo '<div class="wrap"><h1 class="wp-heading-inline">Form Entries</h1>';

        echo '<div class="tablenav top">';
        echo '<div class="alignleft actions">';
        echo $this->render_filter_form($form_names, $months, $selected_form, $selected_month);
        echo '</div>';
        echo '<div class="alignleft actions"></div>';
        $this->render_pagination($page, $total_pages, $total, $current_url);
        echo '<br class="clear"></div>';

        if (empty($filtered_entries)) {
            echo '<p>No entries found.</p>';
        } else {
            echo '<form method="post">';
            echo '<table class="wp-list-table widefat fixed striped posts">';
            echo '<thead><tr>
                    <th scope="col" class="manage-column column-title">Form Name</th>
                    <th scope="col" class="manage-column column-author">Email</th>
                    <th scope="col" class="manage-column column-date">Date</th>
                    <th scope="col" class="manage-column">Actions</th>
                </tr></thead><tbody>';

            foreach ($entries_page as $entry) {
                $date = isset($entry['submission_date']) ? esc_html($entry['submission_date']) : '';
                $form_name = isset($entry['form_name']) ? esc_html($entry['form_name']) : '';
                $email = isset($entry['email']) ? esc_html($entry['email']) : '';
                $entry_content = esc_html(wp_json_encode($entry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                echo "<tr>
                    <td class='column-title column-primary'>$form_name</td>
                    <td class='column-author'>$email</td>
                    <td class='column-date'>$date</td>
                    <td><button type='button' class='button hcdigital-view-entry' data-content='$entry_content'>View</button></td>
                </tr>";
            }
            echo '</tbody></table></form>';

            echo '<div id="hcdigital-entry-modal" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);background:#fff;z-index:9999;padding:24px;max-width:600px;max-height:80vh;overflow:auto;border:1px solid #ccc;box-shadow:0 8px 32px rgba(0,0,0,0.2);"><button id="hcdigital-close-modal" style="float:right;">Close</button><pre id="hcdigital-entry-modal-content" style="white-space:pre-wrap;"></pre></div>';
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('hcdigital-entry-modal');
                var modalContent = document.getElementById('hcdigital-entry-modal-content');
                var closeBtn = document.getElementById('hcdigital-close-modal');
                document.querySelectorAll('.hcdigital-view-entry').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        modalContent.textContent = this.getAttribute('data-content');
                        modal.style.display = 'block';
                    });
                });
                closeBtn.addEventListener('click', function() { modal.style.display = 'none'; });
                window.addEventListener('keydown', function(e) { if (e.key === 'Escape') modal.style.display = 'none'; });
            });
            </script>";

            echo '<div class="tablenav bottom">';
            if (!empty($selected_form)) {
                echo '<div class="alignleft actions">
                    <form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline;">
                        <input type="hidden" name="action" value="hcdigital_export_csv">
                        ' . wp_nonce_field('hcdigital_export_csv_nonce', '_wpnonce', true, false) . '
                        <input type="hidden" name="form_name" value="' . esc_attr($selected_form) . '">
                        <input type="hidden" name="month_year" value="' . esc_attr($selected_month) . '">
                        <input type="submit" class="button action" name="save_csv" value="Save CSV">
                    </form>
                </div>';
                
            }
            $this->render_pagination($page, $total_pages, $total, $current_url);
            echo '<br class="clear"></div>';
        }
        echo '</div>';
    }

    public function export_csv() {
        if (!current_user_can('manage_options') || !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'hcdigital_export_csv_nonce')) {
            wp_die('You are not allowed to do this.');
        }

        $selected_form = isset($_POST['form_name']) ? sanitize_text_field($_POST['form_name']) : '';
        $selected_month = isset($_POST['month_year']) ? sanitize_text_field($_POST['month_year']) : '';

        $filtered_entries = $this->get_form_entries([
            'form_name' => $selected_form,
            'month_year' => $selected_month,
        ]);

        $this->save_csv_data($filtered_entries);
    }

    private function save_csv_data($filtered_entries) {
        if (empty($filtered_entries)) {
            wp_die('No entries to export.');
        }

        $all_keys = [];
        $form_name = '';
        foreach ($filtered_entries as $entry) {
            if (is_array($entry)) {
                $all_keys = array_unique(array_merge($all_keys, array_keys($entry)));
                if (isset($entry['form_name']) && !$form_name) {
                    $form_name = $entry['form_name'];
                }
            }
        }

        $all_keys = array_diff($all_keys, ['__file', '__timestamp']);

        $csv_rows = [];
        $csv_rows[] = $all_keys;

        foreach ($filtered_entries as $entry) {
            $row = [];
            foreach ($all_keys as $key) {
                $val = isset($entry[$key]) ? $entry[$key] : '';
                if (is_array($val)) {
                    $val = implode(', ', $val);
                }
                $row[] = $val;
            }
            $csv_rows[] = $row;
        }

        $filename = ($form_name ? sanitize_file_name($form_name) . '-' : '') . 'form-entries-' . date('Ymd-His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        foreach ($csv_rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}

new hcdigital_Forms_Admin();

<?php
/**
 * Plugin Name: School Opening Hours
 * Plugin URI: https://github.com/allamindit/School-Opening-Hours/
 * Description: This plugin to manage school or any office or shop opening hours and event days.
 * Version: 3.0
 * Author: alamindit
 * Author URI: https://github.com/allamindit
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: school-opening-hours
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('admin_menu', 'school_opening_hours_menu');

function school_opening_hours_menu() {
    add_menu_page('School Opening Hours', 'School Opening Hours', 'manage_options', 'school-opening-hours', 'school_opening_hours_settings_page');
    add_submenu_page('school-opening-hours', 'Event Days', 'Event Days', 'manage_options', 'school-event-days', 'school_event_days_settings_page');
}

function school_opening_hours_settings_page() {
    if (isset($_POST['save_hours']) && check_admin_referer('school_opening_hours_nonce_action', 'school_opening_hours_nonce')) {
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $opening_hours = [];
        foreach ($days as $day) {
            $open_time_key = $day . '_open_time';
            $close_time_key = $day . '_close_time';

            $opening_hours[$day] = [
                'open_time' => isset($_POST[$open_time_key]) ? sanitize_text_field(wp_unslash($_POST[$open_time_key])) : '',
                'close_time' => isset($_POST[$close_time_key]) ? sanitize_text_field(wp_unslash($_POST[$close_time_key])) : '',
            ];
        }
        update_option('school_opening_hours', $opening_hours);

        echo '<div id="message" class="updated notice is-dismissible"><p>Hours saved successfully.</p></div>';
    }

    $opening_hours = get_option('school_opening_hours', []);

    ?>
    <div class="wrap">
        <h1>School Opening Hours</h1>
        <input type="text" value="[school_opening_hours]" id="mysInput" readonly>
        <button class="button-primary" onclick="mysFunction()">Copy Shortcode</button>
        <script>
            function mysFunction() {
                var copyText = document.getElementById("mysInput");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
            }
        </script>
        <p></p>
        <form method="post" action="">
            <?php wp_nonce_field('school_opening_hours_nonce_action', 'school_opening_hours_nonce'); ?>
            <table class="form-table">
                <?php
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                foreach ($days as $day) {
                    $day_key = strtolower($day);
                    $open_time = isset($opening_hours[$day_key]['open_time']) ? $opening_hours[$day_key]['open_time'] : '';
                    $close_time = isset($opening_hours[$day_key]['close_time']) ? $opening_hours[$day_key]['close_time'] : '';
                    ?>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html($day); ?></th>
                        <td>
                            <select name="<?php echo esc_attr($day_key); ?>_open_time">
                                <option value="">Select Open Time</option>
                                <option value="Closed">Closed</option>
                                <?php for ($hour = 0; $hour < 12; $hour++) : ?>
                                    <?php for ($minute = 0; $minute < 60; $minute += 15) : ?>
                                        <option value="<?php echo esc_attr(sprintf('%02d:%02d AM', $hour, $minute)); ?>" <?php selected($open_time, sprintf('%02d:%02d AM', $hour, $minute)); ?>>
                                            <?php echo esc_html(sprintf('%02d:%02d AM', $hour, $minute)); ?>
                                        </option>
                                        <option value="<?php echo esc_attr(sprintf('%02d:%02d PM', $hour, $minute)); ?>" <?php selected($open_time, sprintf('%02d:%02d PM', $hour, $minute)); ?>>
                                            <?php echo esc_html(sprintf('%02d:%02d PM', $hour, $minute)); ?>
                                        </option>
                                    <?php endfor; ?>
                                <?php endfor; ?>
                            </select>
                            to
                            <select name="<?php echo esc_attr($day_key); ?>_close_time">
                                <option value="">Select Close Time</option>
                                <?php for ($hour = 0; $hour < 12; $hour++) : ?>
                                    <?php for ($minute = 0; $minute < 60; $minute += 15) : ?>
                                        <option value="<?php echo esc_attr(sprintf('%02d:%02d AM', $hour, $minute)); ?>" <?php selected($close_time, sprintf('%02d:%02d AM', $hour, $minute)); ?>>
                                            <?php echo esc_html(sprintf('%02d:%02d AM', $hour, $minute)); ?>
                                        </option>
                                        <option value="<?php echo esc_attr(sprintf('%02d:%02d PM', $hour, $minute)); ?>" <?php selected($close_time, sprintf('%02d:%02d PM', $hour, $minute)); ?>>
                                            <?php echo esc_html(sprintf('%02d:%02d PM', $hour, $minute)); ?>
                                        </option>
                                    <?php endfor; ?>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <p><input type="submit" name="save_hours" value="Save Hours" class="button-primary"></p>
        </form>
    </div>
    <?php
}



function school_event_days_settings_page() {
    if (isset($_POST['add_event']) && check_admin_referer('school_event_days_nonce_action', 'school_event_days_nonce')) {
        $event_days = get_option('school_event_days', []);
        if (!is_array($event_days)) {
            $event_days = [];
        }

        $event_days[] = [
            'name' => isset($_POST['event_name']) ? sanitize_text_field(wp_unslash($_POST['event_name'])) : '',
            'date' => isset($_POST['event_date']) ? sanitize_text_field(wp_unslash($_POST['event_date'])) : '',
        ];

        update_option('school_event_days', $event_days);

        echo '<div id="message" class="updated notice is-dismissible"><p>Event added successfully.</p></div>';
    }

    if (isset($_POST['remove_event']) && check_admin_referer('remove_event_nonce_action', 'remove_event_nonce')) {
        $event_days = get_option('school_event_days', []);
        unset($event_days[intval($_POST['remove_event'])]);

        update_option('school_event_days', $event_days);

        echo '<div id="message" class="updated notice is-dismissible"><p>Event removed successfully.</p></div>';
    }

    $event_days = get_option('school_event_days', []);

    ?>
    <div class="wrap">
        <h1>School Event Days</h1>
        <form method="post" action="">
            <?php wp_nonce_field('school_event_days_nonce_action', 'school_event_days_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Event Name</th>
                    <td><input type="text" name="event_name" required></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Event Date</th>
                    <td><input type="date" name="event_date" required></td>
                </tr>
            </table>
            <p><input type="submit" name="add_event" value="+ Add Event" class="button-primary"></p>
        </form>
        <h2>Added Event Days</h2>
        <input type="text" value="[school_event_days]" id="myInput" readonly>
        <button class="button-primary" onclick="myFunction()">Copy Shortcode</button>
        <script>
            function myFunction() {
                var copyText = document.getElementById("myInput");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
            }
        </script>
        <table class="widefat fixed table-custom" cellspacing="0">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" scope="col">#</th>
                    <th id="event_name" class="manage-column column-event_name" scope="col">Event Name</th>
                    <th id="event_date" class="manage-column column-event_date" scope="col">Event Date</th>
                    <th id="event_action" class="manage-column column-event_action" scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($event_days)) : ?>
                    <?php foreach ($event_days as $index => $event) : ?>
                        <tr>
                            <th scope="row" class="check-column"><?php echo esc_html($index + 1); ?></th>
                            <td><?php echo esc_html($event['name']); ?></td>
                            <td><?php echo esc_html($event['date']); ?></td>
                            <td>
                                <form method="post" action="" style="display:inline;">
                                    <?php wp_nonce_field('remove_event_nonce_action', 'remove_event_nonce'); ?>
                                    <input type="hidden" name="remove_event" value="<?php echo esc_attr($index); ?>">
                                    <input type="submit" value="Remove" class="button-secondary">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">No events added.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
// add admin style file
function school_opening_hours_admin_styles() {
    wp_enqueue_style(
        'school-opening-hours-admin-style',
        plugins_url('admin-style.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'admin-style.css')
    );
}
add_action('admin_enqueue_scripts', 'school_opening_hours_admin_styles');
// add fronend style file
function school_opening_hours_frontend_styles() {
    wp_enqueue_style(
        'school-opening-hours-frontend-style',
        plugins_url('frontend-style.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'frontend-style.css')
    );
}
add_action('wp_enqueue_scripts', 'school_opening_hours_frontend_styles');

function school_opening_hours_shortcode() {
    $opening_hours = get_option('school_opening_hours', []);
    ob_start();
    ?>
    <div class="school-opening-hours">
        <ul>
            <?php foreach ($opening_hours as $day => $hours) : ?>
                <li><?php echo esc_html(ucfirst($day)); ?>: <?php echo esc_html($hours['open_time']); ?> - <?php echo esc_html($hours['close_time']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('school_opening_hours', 'school_opening_hours_shortcode');

function school_event_days_shortcode() {
    $event_days = get_option('school_event_days', []);

    ob_start();

    if (!empty($event_days)) {
        echo '<table class="school-event-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>#</th>';
        echo '<th>Event Name</th>';
        echo '<th>Event Date</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($event_days as $index => $event) {
            echo '<tr>';
            echo '<td>' . esc_html($index + 1) . '</td>';
            echo '<td>' . esc_html($event['name']) . '</td>';
            echo '<td>' . esc_html($event['date']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No events added.</p>';
    }

    return ob_get_clean();
}
add_shortcode('school_event_days', 'school_event_days_shortcode');


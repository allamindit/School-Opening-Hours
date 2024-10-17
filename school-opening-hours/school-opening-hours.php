<?php
/**
 * Plugin Name: School Opening Hours
 * Plugin URI: 
 * Description: A plugin to manage school opening hours and event days.
 * Version: 3.0
 * Author: alamindit
 * Author URI: https://github.com/allamindit
 * License: GPL v2 or later
 */

add_action('admin_menu', 'school_opening_hours_menu');

function school_opening_hours_menu() {
    add_menu_page('School Opening Hours', 'School Opening Hours', 'manage_options', 'school-opening-hours', 'school_opening_hours_settings_page');
    add_submenu_page('school-opening-hours', 'Event Days', 'Event Days', 'manage_options', 'school-event-days', 'school_event_days_settings_page');
}

// Function to display the main settings page
function school_opening_hours_settings_page() {
    if (isset($_POST['save_hours']) && check_admin_referer('school_opening_hours_nonce_action', 'school_opening_hours_nonce')) {
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $opening_hours = [];
        foreach ($days as $day) {
            $opening_hours[$day] = [
                'open_time' => sanitize_text_field($_POST[$day . '_open_time']),
                'close_time' => sanitize_text_field($_POST[$day . '_close_time']),
            ];
        }
        update_option('school_opening_hours', $opening_hours);

        // Display success message without redirect
        echo '<div id="message" class="updated notice is-dismissible"><p>Hours saved successfully.</p></div>';
    }

    $opening_hours = get_option('school_opening_hours', []);
    ?>
    <div class="wrap">
	<style>ul#eve_name > li{
    position: relative;
    display: inline-block;
    font-size: 20px;
    color: #000;
    text-transform: capitalize;
    letter-spacing: 0.03em;
    left: 5%;
    border: 1px solid #999;
    padding: 5px;
    text-align: left;
}</style>
<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <h1>School Opening Hours</h1>
		<input type="text" value="[school_opening_hours]" id="mysInput" readonly>
<button class="btn btn-outline-success btn-sm"  onclick="mysFunction()">Copy Shortcode</button>
<script>
function mysFunction() {
  // Get the text field
  var copyText = document.getElementById("mysInput");

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

  // Copy the text inside the text field
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
                                        <option value="<?php echo sprintf('%02d:%02d AM', $hour, $minute); ?>" <?php selected($open_time, sprintf('%02d:%02d AM', $hour, $minute)); ?>>
                                            <?php echo sprintf('%02d:%02d AM', $hour, $minute); ?>
                                        </option>
                                        <option value="<?php echo sprintf('%02d:%02d PM', $hour, $minute); ?>" <?php selected($open_time, sprintf('%02d:%02d PM', $hour, $minute)); ?>>
                                            <?php echo sprintf('%02d:%02d PM', $hour, $minute); ?>
                                        </option>
                                    <?php endfor; ?>
                                <?php endfor; ?>
                            </select>
                            to
                            <select name="<?php echo esc_attr($day_key); ?>_close_time">
                                <option value="">Select Close Time</option>
								
                                <?php for ($hour = 0; $hour < 12; $hour++) : ?>
                                    <?php for ($minute = 0; $minute < 60; $minute += 15) : ?>
                                        <option value="<?php echo sprintf('%02d:%02d AM', $hour, $minute); ?>" <?php selected($close_time, sprintf('%02d:%02d AM', $hour, $minute)); ?>>
                                            <?php echo sprintf('%02d:%02d AM', $hour, $minute); ?>
                                        </option>
                                        <option value="<?php echo sprintf('%02d:%02d PM', $hour, $minute); ?>" <?php selected($close_time, sprintf('%02d:%02d PM', $hour, $minute)); ?>>
                                            <?php echo sprintf('%02d:%02d PM', $hour, $minute); ?>
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

// Function to display the event days settings page
function school_event_days_settings_page() {
    if (isset($_POST['add_event']) && check_admin_referer('school_event_days_nonce_action', 'school_event_days_nonce')) {
        $event_days = get_option('school_event_days', []);
        if (!is_array($event_days)) {
            $event_days = [];
        }

        $event_days[] = [
            'name' => sanitize_text_field($_POST['event_name']),
            'date' => sanitize_text_field($_POST['event_date']),
        ];

        update_option('school_event_days', $event_days);

        // Display success message without redirect
        echo '<div id="message" class="updated notice is-dismissible"><p>Event added successfully.</p></div>';
    }

    if (isset($_POST['remove_event']) && check_admin_referer('remove_event_nonce_action', 'remove_event_nonce')) {
        $event_days = get_option('school_event_days', []);
        unset($event_days[intval($_POST['remove_event'])]);

        update_option('school_event_days', $event_days);

        // Display success message without redirect
        echo '<div id="message" class="updated notice is-dismissible"><p>Event removed successfully.</p></div>';
    }

    $event_days = get_option('school_event_days', []);
    ?>
    <div class="wrap">
	<style>ul#eve_name > li{
    position: relative;
    display: inline-block;
    font-size: 20px;
    color: #000;
    text-transform: capitalize;
    letter-spacing: 0.03em;
    left: 5%;
    border: 1px solid #999;
    padding: 5px;
    text-align: left;
}</style>
<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <h1>School Event Days</h1>

        <!-- Form to add new event -->
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
            <p><input type="submit" name="add_event" value="+ Add Event" class="button-primary"> </p>
        </form>

        <!-- List of added events -->
        <h2>Added Event Days</h2>
		<input type="text" value="[school_event_days]" id="myInput" readonly>
<button class="btn btn-outline-success btn-sm" onclick="myFunction()">Copy Shortcode</button>
<script>
function myFunction() {
  // Get the text field
  var copyText = document.getElementById("myInput");

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

  // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.value);
}
</script>
		<p></p>
		
<br>

        <?php if (!empty($event_days)) : ?>
            <ul  id="eve_name">
                <?php foreach ($event_days as $key => $event) : ?>
                    <li>
                        <?php echo esc_html($event['name']); ?> - <?php echo esc_html($event['date']); ?>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('remove_event_nonce_action', 'remove_event_nonce'); ?>
                            <input type="hidden" name="remove_event" value="<?php echo esc_attr($key); ?>">
          <input type="submit" value="- Remove" class="btn btn-outline-danger btn-sm">
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No events added yet.</p>
        <?php endif; ?>
    </div>
    <?php
}




// Shortcode to display school opening hours
function display_school_opening_hours() {
    $opening_hours = get_option('school_opening_hours', []);

    if (empty($opening_hours)) {
        return '<p>School opening hours not set yet.</p>';
    }

    $output = '<h3>School Opening Hours</h3>';
    $output .= '<table>';
    $days = ['Day Name','Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    foreach ($days as $day) {
        $day_key = strtolower($day);
        $open_time = isset($opening_hours[$day_key]['open_time']) ? $opening_hours[$day_key]['open_time'] : 'Open Time';
        $close_time = isset($opening_hours[$day_key]['close_time']) ? $opening_hours[$day_key]['close_time'] : 'Close Time';

        $output .= '<tr>';
        $output .= '<td><strong>' . esc_html($day) . '</strong></td>';
        $output .= '<td>' . esc_html($open_time) . ' - ' . esc_html($close_time) . '</td>';
        $output .= '</tr>';
    }

    $output .= '</table>';

    return $output;
}
add_shortcode('school_opening_hours', 'display_school_opening_hours');



// Shortcode to display school event days alamindit
function display_school_event_days() {
    $event_days = get_option('school_event_days', []);

    if (empty($event_days)) {
        return '<p>No events added yet.</p>';
    }

    $output = '<h3>School Event Days</h3>';
    $output .= '<ul  id="eve_name">';

    foreach ($event_days as $event) {
        $output .= '<li>';
        $output .= esc_html($event['name']) . ' - ' . esc_html($event['date']);
        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;
}
add_shortcode('school_event_days', 'display_school_event_days');

/*
for style
*/
// Enqueue the CSS for the copy button
function enqueue_copy_code_styles() {
    wp_enqueue_style('copy-code-style', plugin_dir_url(__FILE__) . 'codestyle.css');
}
add_action('wp_enqueue_scripts', 'enqueue_copy_code_styles');

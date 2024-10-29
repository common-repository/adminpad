<?php
/**
 * Plugin Name: AdminPad
 * Plugin URI: https://wordpress.org/plugins/adminpad/
 * Description: Simple note taker for WP site administrators only.
 * Author: Iftekhar Bhuiyan
 * Version: 2.4.4
 * Author URI: https://profiles.wordpress.org/iftekharbhuiyan/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: adminpad
 */
if (!defined('ABSPATH')) {
    exit;
}

if (is_admin()) {

    // register widget
    function bsft_adminpad_widget() {
        if (current_user_can('activate_plugins')) {
            wp_add_dashboard_widget('adminpad', 'AdminPad', 'bsft_adminpad_form');
        }
    }

    // display form and save
    function bsft_adminpad_form() {
        $content = htmlspecialchars_decode(get_option('adminpad_content'));
        $data = empty($content) ? '' : $content;
        // submission check
        if (isset($_POST['adminpad_save']) && isset($_POST['adminpad_nonce']) && wp_verify_nonce($_POST['adminpad_nonce'], basename(__FILE__))) {
            $note = stripslashes_deep(htmlspecialchars($_POST['adminpad_content']));
            if (get_option('adminpad_content') !== false) {
                update_option('adminpad_content', $note);
            } else {
                add_option('adminpad_content', $note);
            }
            echo '<meta http-equiv="refresh" content="0">';
        } else {
            echo '<form action="'.admin_url('index.php').'" method="POST">';
            echo '<input type="hidden" id="adminpad_nonce" name="adminpad_nonce" value="'.wp_create_nonce(basename(__FILE__)).'">';
            echo '<div id="adminpad-content" class="textarea-wrap">';
            wp_editor(
                $data,
                'adminpad_content_id',
                array(
                    'textarea_name' => 'adminpad_content',
                    'media_buttons' => true,
                    'quicktag' => false
                )
            );
            echo '</div>';
            echo '<p style="margin-bottom:0;">';
            echo '<input type="hidden" name="adminpad_save" id="adminpad_save" value="true">';
            echo '<button type="submit" class="button button-primary">Save Note</button>';
            echo '</p></form>';
        }
    }
    
    // uninstalling adminpad
    function bsft_adminpad_uninstall() {
        if (current_user_can('activate_plugins')) {
            delete_option('adminpad_content');
        }
    }
    
    // let's hook
    add_action('wp_dashboard_setup', 'bsft_adminpad_widget');
    register_uninstall_hook( __FILE__, 'bsft_adminpad_uninstall');

}
?>
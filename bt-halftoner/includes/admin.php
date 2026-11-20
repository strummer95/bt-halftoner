<?php
/**
 * BT Halftoner — admin page (what it is, where it shows, updates).
 */
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_menu_page('BT Halftoner', 'BT Halftoner', 'manage_options', 'bt-halftoner', 'bth_admin_page', 'dashicons-image-filter', 58);
});

// Sit with the other BT plugins in the sidebar (BT Quote does the grouping).
add_filter('bt_menu_group_order', function ($w) { $w[] = 'bt halftoner'; return $w; });

function bth_admin_page() {
    if (!current_user_can('manage_options')) return;

    $info   = bth_update_manifest();
    $latest = isset($info['version']) ? $info['version'] : '?';
    $portal = defined('BTP_VERSION');

    echo '<div class="wrap"><h1>BT Halftoner</h1>';
    echo '<p>Turns the see-through parts of a DTF design into solid ink dots that get smaller and further apart, '
       . 'like a screen print. Solid parts stay solid. Every pixel in the file it gives you is full ink or nothing, '
       . 'so a faded colour stops printing pale over the white underbase. Everything runs in the browser; '
       . 'nothing is uploaded or stored.</p>';

    echo '<h2>Status</h2><table class="widefat" style="max-width:640px"><tbody>';
    echo '<tr><td>Installed version</td><td><strong>' . esc_html(BTH_VERSION) . '</strong></td></tr>';
    echo '<tr><td>Latest published</td><td>' . esc_html($latest) . '</td></tr>';
    echo '<tr><td>Employee portal</td><td>' . ($portal
        ? 'Other &gt; Halftoner' . (version_compare(BTP_VERSION, '0.60.0', '>=') ? '' : ' &mdash; <strong>update BT Portal to 0.60.0 or later</strong> to get the tab')
        : 'BT Portal is not active') . '</td></tr>';
    echo '<tr><td>Any other page</td><td><code>[bt_halftoner]</code> &mdash; shows to signed-in staff only</td></tr>';
    echo '</tbody></table>';

    bt_admin_updates_panel(array(
        'slug'     => 'bt-halftoner',
        'version'  => BTH_VERSION,
        'manifest' => 'bth_update_manifest',
        'flush'    => 'bth_force_update_check',
    ));

    echo '</div>';
}

<?php
/*
Plugin Name: BT Halftoner
Plugin URI: https://boomerts.com
Description: Boomer T's DTF halftoner. Turns the see-through parts of a design into printed dots, screen print style, so fades stop printing pink over the white underbase. Shows in the employee portal under Other > Halftoner; shortcode [bt_halftoner].
Version: 0.1.2
Author: Duck and Rabbit Co.
*/

if (!defined('ABSPATH')) exit;

define('BTH_VERSION', '0.1.2');
define('BTH_DIR', plugin_dir_path(__FILE__));
define('BTH_FILE', __FILE__);

require_once BTH_DIR . 'includes/halftoner.php';  // the tool: [bt_halftoner]
require_once BTH_DIR . 'includes/bt-admin.php';   // shared BT updates panel
require_once BTH_DIR . 'includes/admin.php';      // BT Halftoner admin page
require_once BTH_DIR . 'includes/updater.php';    // GitHub self-updater

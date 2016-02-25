<?php
/**
 * Altima LookBook Free Version Uninstall Script
 *
 * Uninstalling Altima LookBook deletes sliders, slides, options and uploaded files.
 *
 * @author      altimawebsystems.com
 * @version     1.0.3
 */


if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

define ( 'ALTIMA_LOOKBOOK_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/alfw_settings.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/alfwManage_files.class.php';

global $wpdb;

/**
 * Cleen options
 */
foreach ($lookbook_settings_fields as $field_name=>$val){
        delete_option($field_name);
}

/**
 * Drop tables
 */
    $wpdb->query("DROP TABLE `" . $wpdb->prefix . SLIDES_TABLE . "`");
    $wpdb->query("DROP TABLE `" . $wpdb->prefix . SLIDER_TABLE . "`");

/**
 * Remove uploaded files
 */
$file = new alfw_manage_files();
$file->delete_directory(FULL_UPLOAD_PATH);
$file->delete_directory(FULL_UPLOAD_PATH_THUMB);
$file->delete_directory(FULL_UPLOAD_PATH_ORIG);

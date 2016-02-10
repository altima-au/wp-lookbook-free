<?php
/**
*    Plugin Name: Altima LookBook Free Version
*    Description: Slider with Hotspot points
*    Text Domain: http://altimawebsystems.com/
*    Version: 1.0
*    Author: altimawebsystems.com
*    Tested up to: 4.3.1
*/
require_once( ABSPATH . 'wp-includes/pluggable.php' );
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

$upload_dir_info = wp_upload_dir();

define ( 'ALTIMA_LOOKBOOK_VERSION', '0.1' );
define ( 'ALTIMA_LOOKBOOK_BASENAME', plugin_basename( __FILE__ ) );
define ( 'ALTIMA_LOOKBOOK_NAME', trim( dirname( ALTIMA_LOOKBOOK_BASENAME ), '/' ) );
define ( 'ALTIMA_LOOKBOOK_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define ( 'ALTIMA_LOOKBOOK_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define ( 'UPLOAD_FOLDER_NAME', 'sliders_free');
define ( 'UPLOAD_FOLDER_NAME_THUMB', 'sliders_free_thumb');
define ( 'UPLOAD_FOLDER_NAME_ORIG', 'sliders_free_orig');
define ( 'FULL_UPLOAD_PATH', $upload_dir_info['basedir'] . '/' . UPLOAD_FOLDER_NAME);
define ( 'FULL_UPLOAD_PATH_ORIG', $upload_dir_info['basedir'] . '/' . UPLOAD_FOLDER_NAME_ORIG);
define ( 'FULL_UPLOAD_PATH_THUMB', $upload_dir_info['basedir'] . '/' . UPLOAD_FOLDER_NAME_THUMB);
define ('SLIDER_TABLE', 'lookbook_sliders_free');
define ('SLIDES_TABLE', 'lookbook_slides_free');

require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/settings.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/functions.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/s_session.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/manage_files.class.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/slider.php';

if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', 'woocommerce_not_install_notice' );
}

$wp_session = new s_session();

if ( is_admin() ) {
    require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/admin/admin.php';
}

/**
 * Init
 */
add_action('init', 'lookbook_init');
add_action('admin_menu', 'lookbook_add_menu');
add_action('admin_head', 'admin_css' );
add_action('admin_head', 'admin_js');

function admin_css(){
    echo '<link rel="stylesheet" type="text/css" href="'.ALTIMA_LOOKBOOK_PLUGIN_URL.'/admin/css/lighttabs.css">';
    echo '<link rel="stylesheet" type="text/css" href="'.ALTIMA_LOOKBOOK_PLUGIN_URL.'/admin/css/jquery-ui.css">';
    echo '<link rel="stylesheet" type="text/css" href="'.ALTIMA_LOOKBOOK_PLUGIN_URL.'/admin/css/jquery-ui.theme.css">';
    echo '<link rel="stylesheet" type="text/css" href="'.ALTIMA_LOOKBOOK_PLUGIN_URL.'/admin/css/main.css">';
    echo '<link rel="stylesheet" type="text/css" href="'.ALTIMA_LOOKBOOK_PLUGIN_URL.'/admin/css/annotation.css">';
}

function admin_js(){
    echo '<script type="text/javascript" src="' . ALTIMA_LOOKBOOK_PLUGIN_URL .'/admin/js/jquery-ui-1.9.1.js"></script>';
    echo '<script type="text/javascript" src="' . ALTIMA_LOOKBOOK_PLUGIN_URL .'/admin/js/lighttabs.js"></script>';
    echo '<script type="text/javascript" src="' . ALTIMA_LOOKBOOK_PLUGIN_URL .'/assets/js/jquery.actual.js"></script>';
    echo '<script type="text/javascript" src="' . ALTIMA_LOOKBOOK_PLUGIN_URL .'/admin/js/scripts.js"></script>';
}


function lookbook_init() {
    do_action( 'lookbook_init' );
}

add_action('wp_print_scripts', 'load_slider_scripts');

function load_slider_scripts() {
    wp_enqueue_style ('lookbook');
    wp_enqueue_script('cycle2');
    wp_enqueue_script('caption2');
}

function lookbook_install () {
    global $wpdb;
	global $lookbook_settings_fields;
	
    $table_name = $wpdb->prefix . SLIDER_TABLE;

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name ."` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `width` smallint(5) unsigned NOT NULL,
                  `height` smallint(5) unsigned NOT NULL,
                  `slider_effect` text,
                  `show_navigation` tinyint(1) unsigned NOT NULL,
                  `navigation_on_hover_state_only` tinyint(1) unsigned NOT NULL,
                  `show_thumbnails` tinyint(1) unsigned NOT NULL,
                  `deny_resize_img` tinyint(1) unsigned NOT NULL,
                  `pause` smallint(5) unsigned NOT NULL,
                  `transition_duration` smallint(5) unsigned NOT NULL,
                  `content_before` longtext NOT NULL,
                  `content_after` longtext NOT NULL,
                  `status` tinyint(1) unsigned NOT NULL,
                  `show_slide_caption` tinyint(1) NOT NULL,
                  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `thumb_width` smallint(5) unsigned NOT NULL,
                  `thumb_height` smallint(5) unsigned NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        dbDelta($sql);

        $wpdb->query('INSERT INTO
              `wp_lookbook_sliders_free` (`id`, `name`, `width`, `height`, `slider_effect`, `show_navigation`, `navigation_on_hover_state_only`, `show_thumbnails`, `deny_resize_img`, `pause`, `transition_duration`, `content_before`, `content_after`, `status`, `show_slide_caption`, `updated`, `created`, `thumb_width`, `thumb_height`)
            VALUES
              (1, \'Slide 1\', 1024, 500, \'a:1:{i:0;s:4:"fade";}\', 0, 0, 1, 0, 4000, 1500, \'\', \'\', 1, 0, \'0000-00-00 00:00:00\', \'0000-00-00 00:00:00\', 150, 100);');

    }

    $table_name = $wpdb->prefix . SLIDES_TABLE;

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `slider_id` int(10) unsigned NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `caption` text NOT NULL,
                  `order_flag` tinyint(127) unsigned NOT NULL,
                  `link` varchar(255) NOT NULL,
                  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
                  `picture` varchar(255) NOT NULL,
                  `hotsposts` text NOT NULL,
                  `order` tinyint(3) unsigned NOT NULL,
                  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        dbDelta($sql);
    }

    /**
     * Store defaults options
     */
      foreach ($lookbook_settings_fields as $key=>$val) {

          $result = $wpdb->get_results(
              "SELECT
                  option_id
              FROM
                  `" . $wpdb->prefix . "options" . "`
              WHERE
                option_name = '$key'",
              ARRAY_A);

          if (empty($result)) {
              $wpdb->insert(
                    $wpdb->prefix . 'options',
                        array(
                            'option_name' => $key,
                            'option_value' => $val
                        ),
                    array(
                        '%s',
                        '%s'
                    )
                );
          }
        }

    /**
     * Create default folder for picture
     */
    $file = new manage_files();
    $file->create_folder_recursive(FULL_UPLOAD_PATH);
    $file->create_folder_recursive(FULL_UPLOAD_PATH_THUMB);
    $file->create_folder_recursive(FULL_UPLOAD_PATH_ORIG);
}

register_activation_hook(__FILE__, 'lookbook_install');

function lookbook_add_menu() {
    add_menu_page(__('LookBookFree'), __('LookBookFree'), 'edit_pages', 'lookbook', 'dushboard');
}

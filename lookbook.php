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

define ( 'ALTIMA_LOOKBOOK_VERSION', '1.0' );
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
define ( 'SLIDER_TABLE', 'lookbook_sliders_free');
define ( 'SLIDES_TABLE', 'lookbook_slides_free');

require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/alfw_settings.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/alfw_functions.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/alfw_s_session.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/alfwManage_files.class.php';
require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/includes/alfw_slider.php';

if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', 'alfw_woocommerce_not_install_notice' );
}

$wp_session = new alfw_s_session();

function admin_css(){
    wp_register_style( 'lighttabs', plugins_url( 'admin/css/lighttabs.css', __FILE__), array(), '', 'all' );
    wp_register_style( 'jquery-ui', plugins_url( 'admin/css/jquery-ui.css', __FILE__), array(), '', 'all' );
    wp_register_style( 'jquery-ui_theme', plugins_url( 'admin/css/jquery-ui.theme.css', __FILE__), array(), '', 'all' );
    wp_register_style( 'main', plugins_url( 'admin/css/main.css', __FILE__), array(), '', 'all' );
    wp_register_style( 'annotation', plugins_url( 'admin/css/annotation.css', __FILE__), array(), '', 'all' );

    wp_enqueue_style( 'lighttabs' );
    wp_enqueue_style( 'jquery-ui' );
    wp_enqueue_style( 'jquery-ui_theme' );
    wp_enqueue_style( 'main' );
    wp_enqueue_style( 'annotation' );
}

function wpcycle_scripts_load() {
    wp_register_script( 'cycle2', plugins_url( 'assets/js/cycle2/jquery.cycle2.min.js', __FILE__) );
    wp_register_script( 'caption2', plugins_url( 'assets/js/cycle2/jquery.cycle2.caption2.min.js', __FILE__) );
    wp_register_script( 'hotspots', plugins_url( 'admin/js/hotspots.js', __FILE__) );
    wp_register_script( 'actual', plugins_url( 'assets/js/jquery.actual.js', __FILE__) );
    wp_register_script( 'annotate', plugins_url( 'admin/js/jquery.annotate.js', __FILE__) );
    wp_register_script( 'carousel', plugins_url( 'assets/js/cycle2/jquery.cycle2.carousel.min.js', __FILE__) );
    wp_register_script( 'flip', plugins_url( 'assets/js/cycle2/jquery.cycle2.flip.min.js', __FILE__) );
    wp_register_script( 'scrolV', plugins_url( 'assets/js/cycle2/jquery.cycle2.scrollVert.min.js', __FILE__) );
    wp_register_script( 'shuffle', plugins_url( 'assets/js/cycle2/jquery.cycle2.shuffle.min.js', __FILE__) );
    wp_register_script( 'tile', plugins_url( 'assets/js/cycle2/jquery.cycle2.tile.min.js', __FILE__) );
    wp_register_script( 'additionalEffect', plugins_url( 'assets/js/cycle2/jquery.cycle2.addEffects.js', __FILE__) );
    wp_register_script( 'swipe', plugins_url( 'assets/js/cycle2/jquery.cycle2.swipe.min.js', __FILE__) );

    wp_register_style( 'lookbook', plugins_url( 'assets/css/lookbook.css', __FILE__), array(), '', 'all' );
}

function admin_js(){
    wp_register_script( 'jquery-ui', plugins_url( 'admin/js/jquery-ui.min.js', __FILE__ ) );
    wp_register_script( 'lighttabs', plugins_url( 'admin/js/lighttabs.js', __FILE__ ) );
    wp_register_script( 'jquery_actual', plugins_url( 'assets/js/jquery.actual.js', __FILE__ ) );
    wp_register_script( 'scripts', plugins_url( 'admin/js/scripts.js', __FILE__ ) );

    wp_enqueue_script('jquery-ui');
    wp_enqueue_script('lighttabs');
    wp_enqueue_script('jquery_actual');
    wp_enqueue_script('scripts');
}

/**
 * Init
 */
add_action( 'init', 'lookbook_init' );
add_action( 'admin_menu', 'lookbook_add_menu' );
add_action( 'wp_enqueue_scripts', 'wpcycle_scripts_load' );
add_action( 'admin_enqueue_scripts', 'wpcycle_scripts_load' );
add_action( 'admin_enqueue_scripts', 'admin_css' );
add_action( 'admin_enqueue_scripts', 'admin_js' );


if ( is_admin() ) {
    if( current_user_can('manage_options') ){
        require_once ALTIMA_LOOKBOOK_PLUGIN_DIR . '/admin/admin.php';
    }
}

function lookbook_init() {
    do_action( 'lookbook_init' );
}

add_action('wp_print_scripts', 'load_slider_scripts');

function load_slider_scripts() {
    wp_enqueue_style ('lookbook');
    wp_enqueue_script ('cycle2');
    wp_enqueue_script ('caption2');
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
    $file = new alfw_manage_files();
    $file->create_folder_recursive(FULL_UPLOAD_PATH);
    $file->create_folder_recursive(FULL_UPLOAD_PATH_THUMB);
    $file->create_folder_recursive(FULL_UPLOAD_PATH_ORIG);
}

register_activation_hook(__FILE__, 'lookbook_install');

function lookbook_add_menu() {
    add_menu_page(__('LookBookFree'), __('LookBookFree'), 'manage_options', 'lookbook', 'alfw_dashboard');
}

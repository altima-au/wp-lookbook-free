<?php

$upload_dir_info = wp_upload_dir();

define ( 'UPLOAD_FOLDER_NAME', 'sliders_free');
define ( 'UPLOAD_FOLDER_NAME_THUMB', 'sliders_free_thumb');
define ( 'UPLOAD_FOLDER_NAME_ORIG', 'sliders_free_orig');

define ( 'SLIDER_TABLE', 'lookbook_sliders_free');
define ( 'SLIDES_TABLE', 'lookbook_slides_free');

define ( 'FULL_UPLOAD_PATH', $upload_dir_info['basedir'] . '/' . UPLOAD_FOLDER_NAME);
define ( 'FULL_UPLOAD_PATH_ORIG', $upload_dir_info['basedir'] . '/' . UPLOAD_FOLDER_NAME_ORIG);
define ( 'FULL_UPLOAD_PATH_THUMB', $upload_dir_info['basedir'] . '/' . UPLOAD_FOLDER_NAME_THUMB);

/**
 * Plugin fields options
 */
global $lookbook_settings_fields;

$lookbook_settings_fields = array (
    'wplb_free_max_file_size' => 20000000,
    'wplb_free_allow_ext' => 'png,gif,jpg',
    'wplb_free_hotspots_overlap' => 1,
    'wplb_free_show_desc_in_popup' => 1,
    'wplb_free_show_addcart_in_popup' => 1,
    'wplb_free_show_pinit_slide' => '',
    'wplb_free_hspt_icon' => '',
    'wplb_free_thumb_height' => 100,
    'wplb_free_thumb_width' => 100,
    'wplb_free_debug_domain'=>1
);

/**
 * Options for select
 */
$lookbook_slider_effects = array(
    "all" =>"All effects",
    "none" =>"None effects",
    "fade" =>"Fade",
    "fadeout" =>"Fade Out",
    "flipHorz" =>"Flip horz",
    "flipVert" =>"Flip vert"
);

/**
 * Possible Handlers
 */
$admin_get_handlers = array('add_slider','manage_slides','add_slides','view_slider');
$admin_post_handlers = array('store_slider','store_slide','del_slides','del_slider','check_post_id','ajax_upload','store_options');
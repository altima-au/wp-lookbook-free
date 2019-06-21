<?php
/**
 *    Plugin Name: Altima LookBook Free Version
 *    Description: Slider with Hotspot points
 *    Text Domain: http://altimawebsystems.com/
 *    Version: 1.0.10
 *    Author: altimawebsystems.com
 *    Tested up to: 5.2.1
 */

$url_tail = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_REQUEST['lb_action'])) {
        if (in_array($_REQUEST['lb_action'], $admin_post_handlers)) {
            call_user_func($_GET['lb_action']);
        }
    }

    if (!isset($_POST['noredirect']) && isset($_REQUEST['page']) && $_REQUEST['page'] == 'lookbook') {
        $site_url = esc_url( home_url( '/' ) );
        wp_redirect($site_url . "wp-admin/admin.php?page=lookbook".$url_tail);
        exit;
    }

}

function alfw_dashboard(){
    global $admin_get_handlers;
    $error_msg = prepare_upload_msg();
    if (!empty($error_msg)) {
        $error_msg = '<p class="operation_error">' . $error_msg . '</p>';
    }

    echo '<h2>' . __('Lookbook Slider Manager') . '</h2><div id="msg">'.$error_msg.'</div>';
	
	echo '
	<div class="lookbook_promo">
    	<a class="altima_logo_firm" target="_blank" href="http://altimawebsystems.com/"><img src="'.ALTIMA_LOOKBOOK_PLUGIN_URL.'/admin/images/altima_logo.png"></a>
        <p>Need unlimited number of sliders, slides and hotspots and priority support?</p>
        <a target="_blank" class="get_pro" href="https://shop.altima.net.au/woocommerce-lookbook-professional.html"><img src="'.ALTIMA_LOOKBOOK_PLUGIN_URL.'/admin/images/ico_basket.png">Check Altima Lookbook Pro For WooCommerce!</a>
    </div>';
	
	

    if (isset($_GET['lb_action'])) {

        if (in_array($_GET['lb_action'], $admin_get_handlers)){
            call_user_func($_GET['lb_action']);
        }

    }else {

        echo '<div class="tabs">';
            echo '
            <ul>
                <li>' . __('Manage Slides') . '</li>
                <li>' . __('Settings') . '</li>
            </ul>
            <div>';

            //list_sliders();
            manage_slides();

            lb_settings();

        echo '</div>';
    }
}

function lb_settings() {
    global $lookbook_settings_fields;
        ?>
        <div>

        <form method="post" action="admin.php?page=lookbook&lb_action=store_options">
            <input type="hidden" name="page_options" value="<?php echo implode(',', array_keys($lookbook_settings_fields));?>" />

            <?php wp_nonce_field( 'store_options', '_alfw_nonce'); ?>

        <table class="wp-list-table widefat fixed striped pages">

            <tr id="row_lookbookslider_general_max_upload_filesize">
                <td class="label">
                    <label for="lookbookslider_general_max_upload_filesize"><?php echo __('Uploaded file max size (bytes)');?></label>
                </td>
                <td class="value">
                    <input type="text" class=" validate-digits required-entry input-text" value="<?php echo get_option('wplb_free_max_file_size');?>" name="wplb_free_max_file_size" id="lookbookslider_general_max_upload_filesize">
                    <p class="note"><span><?php echo __('Must be less then upload_max_filesize and post_max_size in php.ini');?></span></p>
                </td>
                <td class=""></td>
            </tr>

            <tr id="row_lookbookslider_general_allowed_extensions">
                <td class="label">
                    <label for="lookbookslider_general_allowed_extensions"><?php echo __('Allowed extensions');?></label>
                </td>
                <td class="value">
                    <input type="text" class=" input-text" value="<?php echo get_option('wplb_free_allow_ext');?>" name="wplb_free_allow_ext" id="lookbookslider_general_allowed_extensions">
                    <p class="note"><span><?php echo __('Comma separated file extensions. Example, " jpg,gif,png "');?></span></p>
                </td>
                <td class=""></td>
            </tr>
            <tr id="row_lookbookslider_general_interdict_areas_overlap">
                <td class="label">
                    <label for="lookbookslider_general_interdict_areas_overlap"><?php echo __('Disallow hotspots areas overlap');?></label>
                </td>
                <td class="value">
                        <?php
                            echo alfw_form_select( 'wplb_free_hotspots_overlap', get_option('wplb_free_hotspots_overlap'), array('0'=>__('No'), '1'=>__('Yes')), array('class'=>'select', 'id'=>'lookbookslider_general_interdict_areas_overlap') );
                        ?>
                        <p class="note"><span><?php echo __('If "Yes", will disallow hotspots areas overlap');?></span></p>
                </td>
                <td class=""></td>
            </tr>

    <?php

        add_slider();
    ?>
        </table>
            <div class="bottom_button">
                <?php echo '<input type="submit" value="'.__('Save').'" class="button button-primary button-large">';?>
            </div>
        </form>
    <?php
        echo '</div>';

}

function list_sliders() {

    global $wpdb;

    echo '<div>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th class="dies-column">#</th>
                    <th>'.__('Name').'</th>
                    <th>'.__('Slides').'</th>
                    <th class="column-size">'.__('Slider Size').'</th>
                    <th class="status-column">'.__('Status').'</th>
                </tr>
            </thead>';

    $result = $wpdb->get_results(
            "SELECT
                id, name, width, height, status
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "`
             ORDER BY
                name
             ASC",
        ARRAY_A);

    $s = 1;
    foreach ($result as $slider){
        $status = $slider['status'] ? __('Active') : __('Not active');
        echo
            '<tr>
                <td>'.$s.'</td>
                <td>
                    <strong><a href="admin.php?page=lookbook&lb_action=add_slider&id='.$slider['id'].'">' . $slider['name'] . '</a><br>
                    <div class="row-actions">

                    <span class="edit"><a href="admin.php?page=lookbook&lb_action=add_slider&id='.$slider['id'].'">'.__('Edit').'</a>|</span>
                    <span class="view"><a href="admin.php?page=lookbook&lb_action=view_slider&id='.$slider['id'].'">'.__('View').'</a>|</span>
                    <span class="edit"><a href="#" onclick="show_popup('.$slider['id'].')" class="getshcde">'.__('Get Shortcode').'</a></span>

                    <div id="dialog-message_' . $slider['id'] . '" title="'.__('Get shortcode for').' [' . $slider['name'] . ']" class="shotcode_dialog">
                      <p><span style="white-space: nowrap;">[slider_render slider_id="'.$slider['id'].'"]</span></p>
                      <p>'.__('copy and paste to the page where you want to embed it').'</p>
                    </div>

                    </div>
                </td>
                <td><a href="admin.php?page=lookbook&lb_action=manage_slides&id='.$slider['id'].'">'.__('Manage Slides').'</a></td>
                <td>'. $slider['width'].'x'.$slider['height'] . '</td>
                <td>'.$status.'</td>
            </tr>';
        $s++;
    }

    echo '</table>
    </div>';
}


function add_slider($slider_id = 1) {

    global $lookbook_slider_effects, $wpdb;

    $res = array();
    $res = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                *
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "`
            WHERE
                id = %d",
            $slider_id
        ),
        ARRAY_A
    );

    echo
        '
            <input type="hidden" name="slider_id" value="1" />
            <tr>
                <td class="label">
                    <label for="slider_width">'.__('Slider Width (px)').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="number" required class=" input-text" value="'.$res[0]['width'].'" name="lb_width" id="slider_width">
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="slider_height">'.__('Slider Height (px)').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="number" required class=" input-text" value="'.$res[0]['height'].'" name="lb_height" id="slider_height">
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="slider_thumb_width">'.__('Slider Thumbnail Width (px)').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="number" required class=" input-text" value="'.$res[0]['thumb_width'].'" name="lb_thumb_width" id="slider_thumb_width">
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="slider_thumb_height">'.__('Slider Thumbnail Height (px)').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="number" required class=" input-text" value="'.$res[0]['thumb_height'].'" name="lb_thumb_height" id="slider_thumb_height">
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="slider_effect">'.__('Transition effect').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_slider_effect[]', $res[0]['slider_effect'], $lookbook_slider_effects, $attrbutes = array("multiple"=>"multiple", "size"=>"10", "id"=>"slider_effect", "class"=>"lb_multisel")) .'
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="show_navigation">'.__('Show navigation').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_show_navigation', $res[0]['show_navigation'], array('0'=>__('No'), '1'=>__('Yes')), $attrbutes = array( "id"=>"show_navigation")) .'
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="navigation_on_hover_state_only">'.__('Navigation on hover state only').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_navigation_on_hover_state_only', $res[0]['navigation_on_hover_state_only'], array('0'=>__('No'), '1'=>__('Yes')), $attrbutes = array( "id"=>"navigation_on_hover_state_only")) .'
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="show_thumbnails">'.__('Show thumbnails').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_show_thumbnails', $res[0]['show_thumbnails'], array('0'=>__('No'), '1'=>__('Yes')), $attrbutes = array( "id"=>"show_thumbnails")) .'
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="deny_resize_img">'.__('Deny resize images').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_deny_resize_img', $res[0]['deny_resize_img'], array('0'=>__('No'), '1'=>__('Yes')), $attrbutes = array( "id"=>"deny_resize_img")) .'
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="pause">'.__('Pause (ms)').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="number" required class=" input-text" value="'.$res[0]['pause'].'" name="lb_pause" id="pause">
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="transition_duration">'.__('Transition duration (ms)').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="number" required class=" input-text" value="'.$res[0]['transition_duration'].'" name="lb_transition_duration" id="transition_duration">
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="content_before">'.__('Content Before').'</label>
                </td>
                <td class="value">
                    <textarea class="lb_textarea" name="lb_content_before" id="content_before">'.$res[0]['content_before'].'</textarea>
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="content_after">'.__('Content After').'</label>
                </td>
                <td class="value">
                    <textarea class="lb_textarea" name="lb_content_after" id="content_after">'.$res[0]['content_after'].'</textarea>
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="status">'.__('Status').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_status', $res[0]['status'], array('0'=>__('Disabled'), '1'=>__('Enabled')), $attrbutes = array( "id"=>"status")) .'
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td class="label">
                    <label for="show_slide_caption">'.__('Show Slide Caption').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_show_slide_caption', $res[0]['show_slide_caption'], array('0'=>__('No'), '1'=>__('Yes')), $attrbutes = array( "id"=>"show_slide_caption")) .'
                </td>
                <td class=""></td>
            </tr>

        ';
}

function store_slider() {
    global $wpdb, $wp_session;
    $wpdb->show_errors();
    $user_data = prepare_data($_POST);

    /**
     * Checking if need resize already created images
     */
    $check_res = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                *
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "`
                WHERE
                    id = %d",
            $_POST['slider_id']
        ),
        ARRAY_A
    );

    if($check_res[0]['id']) {
        /**
         * Update
         */
    if ($wpdb->update(
                    $wpdb->prefix . SLIDER_TABLE,
                    $user_data['data'],
                    array('id'=>$_POST['slider_id']),
                    $user_data['format'],
                    array( '%d' )
                    )
            ) {

            /**
             * Main picture resize
             */
            if ( $check_res[0]['width'] != $user_data['data']['width'] || $check_res[0]['height'] != $user_data['data']['height']) {
                packet_resize($_POST['slider_id'], UPLOAD_FOLDER_NAME, array('width'=>'width', 'height'=>'height'));
            }

            /**
             * Thumbnail picture resize
             */
            if ( $check_res[0]['thumb_width'] != $user_data['data']['thumb_width'] || $check_res[0]['thumb_height'] != $user_data['data']['thumb_height']) {
                packet_resize($_POST['slider_id'], UPLOAD_FOLDER_NAME_THUMB, array('width'=>'thumb_width', 'height'=>'thumb_height'));
            }

            $error_statuses[] = __('Information saved');
        } else {
            $error_statuses[] = __('Information not saved');
        }
    }else{
        /**
         * Insert
         */
        if ($wpdb->insert(
                $wpdb->prefix . SLIDER_TABLE,
                $user_data['data'],
                $user_data['format']
        )
        ){
            $error_statuses[] = __('Information saved');
        } else {
            $error_statuses[] = __('Information not saved');
        }
    }
    $wp_session->s_set('errors', $error_statuses);
}

function manage_slides($slider_id = 1) {
    global $wpdb;

    $upload_dir_info = wp_upload_dir();

    $error_msg = prepare_upload_msg();
    if (!empty($error_msg)) {
        $error_msg = '<p class="operation_error">' . $error_msg . '</p>';
    }

    $wpdb->show_errors();

    $slider_name = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT
                name
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "`
            WHERE
                id = %d
            ",
            $slider_id
        )
    );

    $limit_result = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                COUNT(id) AS slides_count
            FROM
                `" . $wpdb->prefix . SLIDES_TABLE . "`
            WHERE
                slider_id = %d
            ",
            $slider_id
        ),
        ARRAY_A
    );

    if ($limit_result['0']['slides_count'] < 5) {
        $add_new_slide = '<a href="admin.php?page=lookbook&lb_action=add_slides&slider_id='.$slider_id.'" class="page-title-action">'.__('Add Slide').'</a>';
    }else {
        $add_new_slide = __('Only up to 5 slides could be uploaded in Free Lookbook plugin.');
    }

    echo '<div>
        <div><div id="msg">'.$error_msg.'</div>
            <div class="slide_shotrcode_exampl"><h2>'.__('Add Lookbook shortcode:') . ' <span class="flash_code">[slider_render slider_id="' . $slider_id . '"]</span>' . __('copy and paste to the page where you want to embed it') .  '</h2></div>
            <div class="wrap">
                <h1>
                    ' . $add_new_slide . '
                </h1>
            </div>
        </div>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th class="dies-column">#</th>
                    <th>'.__('Image').'</th>
                    <th>'.__('Name').'</th>
                    <th>'.__('Order').'</th>
                    <th>'.__('Status').'</th>
                </tr>
            </thead>';

    $result = $wpdb->get_results(
            $wpdb->prepare(
            "SELECT
                id, name, status, picture, order_flag
            FROM
                `" . $wpdb->prefix . SLIDES_TABLE . "`
            WHERE
                slider_id = %d
            ORDER BY
                    order_flag
            ASC",
                $slider_id
            ),
        ARRAY_A
    );

    $s = 1;
    foreach ($result as $slider){
        $status = $slider['status'] ? __('Active') : __('Not active');
        echo
            '<tr>
                <td>'.$s.'</td>
                <td><img src="' . $upload_dir_info['baseurl'] . '/' .UPLOAD_FOLDER_NAME_THUMB . "/" . $slider_id . "/" . $slider['picture'].'" /></td>
                <td>
                    <strong><a href="admin.php?page=lookbook&lb_action=add_slides&slider_id='.$slider_id.'&id='.$slider['id'].'">' . $slider['name'] . '</a><br>
                    <div class="row-actions">
                        <span class="edit"><a href="admin.php?page=lookbook&lb_action=add_slides&slider_id='.$slider_id.'&id='.$slider['id'].'">'.__('Edit').'</a>|</span>
                        <span class="delete">
                            <form id="del'.$slider['id'].'" method="post" action="admin.php?page=lookbook&lb_action=del_slides">
                            <input type="hidden" name="id" value="'.$slider['id'].'">
                            <input type="hidden" name="slider_id" value="'.$slider_id.'">
                            <input type="hidden" name="lb_action" value="del_slides">
                            <a href="#" class="delete-tag" onclick="if(confirm(\'Delete ?\')) jQuery(\'#del'.$slider['id'].'\').submit(); else return false;">'.__('Delete').'</a>';
                            wp_nonce_field( 'del_slides', '_alfw_nonce');
                        echo '</form>|
                        </span>
                    </div>
                </td>
                <td>'.$slider['order_flag'].'</td>
                <td>'.$status.'</td>
            </tr>';
        $s++;
    }

    echo '</table>
    </div>';
}

function store_slide() {
    global $url_tail, $wp_session, $wpdb;

    if ( ! empty( $_POST ) && check_admin_referer( 'store_slide', '_alfw_nonce' ) ) {

        $file = new alfw_manage_files();

        $wpdb->show_errors();
        $user_data = prepare_data($_POST);

        $create_slide = false;

            if (empty($_POST['slide_id'])) {
                /**
                 * Insert
                 */
                if ($wpdb->insert(
                    $wpdb->prefix . SLIDES_TABLE,
                    $user_data['data'],
                    $user_data['format']
                )
                ){
                    $error_statuses[] = __('Information saved');
                } else {
                    $error_statuses[] = __('Information not saved');
                }
                $slide_id = $wpdb->insert_id;
                $create_slide = true;
            }else {
                /**
                 * Update
                 */
                if ($wpdb->update(
                    $wpdb->prefix . SLIDES_TABLE,
                    $user_data['data'],
                    array('id'=>$_POST['slide_id']),
                    $user_data['format'],
                    array( '%d' )
                )
                ){
                    $error_statuses[] = __('Information saved');
                } else {
                    $error_statuses[] = __('Information not saved');
                }
                $slide_id = $_POST['slide_id'];
            }

            if ($_POST['tmp_picture']) {

                /**
                 * Get slider options
                 */
                $slider_options = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT
                            id, deny_resize_img, width, height, thumb_width, thumb_height
                        FROM
                            `" . $wpdb->prefix . SLIDER_TABLE . "`
                    WHERE
                        id = %d
                    ",
                        $user_data['data']['slider_id']
                    ),
                    ARRAY_A
                );

                $upload_dir_info = wp_upload_dir();
                $upload_dir_info = $upload_dir_info['basedir'];

                /**
                 * Store original image
                 */
                $file->create_folder_recursive(FULL_UPLOAD_PATH_ORIG . "/" . $slider_options[0]['id'], 0755);
                //$picture_name = $file->modif_file_name(FULL_UPLOAD_PATH_ORIG . "/" . $slider_options[0]['id'] . "/", $_POST['tmp_picture_name']);
                $picture_name = $file->copy_file($upload_dir_info . "/" . $_POST['tmp_picture'], FULL_UPLOAD_PATH_ORIG . "/" . $slider_options[0]['id'] . "/", $_POST['tmp_picture_name']);
                /*
                if (!copy($upload_dir_info . "/" . $_POST['tmp_picture'], FULL_UPLOAD_PATH_ORIG . "/" . $slider_options[0]['id'] . "/" . $_POST['tmp_picture_name'])){
                    throw new Exception('File can\'t copyed to destination folder!');
                }
                */
                /**
                 * Work with thumb
                 */
                $thumb_width = $slider_options[0]['thumb_width'];
                $thumb_height = $slider_options[0]['thumb_height'];

                /**
                 * Check orientation
                 */
                $thumb_check_result = $file->check_orientation(
                                        $thumb_width,
                                        $thumb_height,
                                        $upload_dir_info . "/" .$_POST['tmp_picture']
                );

                $force_resize_flag = true;

                /**
                 * Different orientation
                 */
                $need_places_thmb = false;
                if (!$thumb_check_result['check']){
                    $force_resize_flag = false;
                    $thumb_height = $thumb_check_result['resize_height'];
                    $thumb_width = $thumb_check_result['resize_width'];
                    $need_places_thmb = true;
                }

                $file->resize_upload_img(
                        $upload_dir_info . "/" .$_POST['tmp_picture'],
                        $picture_name,
                        $thumb_height,
                        $thumb_width,
                        FULL_UPLOAD_PATH_THUMB . "/" . $slider_options[0]['id'] . "/",
                        $force_resize_flag
                );

                /**
                 * Centered image on canvas
                 */
                if ($need_places_thmb) {
                    $file->center_place_img_to_canvas(
                            $slider_options[0]['thumb_width'],
                            $slider_options[0]['thumb_height'],
                            $thumb_check_result['frame_orient'],
                            FULL_UPLOAD_PATH_THUMB . "/" . $slider_options[0]['id'] . "/" . $picture_name
                    );
                }

                /**
                 * Work with main picture
                 */

                /**
                 * Check orientation
                 */
                $check_result = $file->check_orientation(
                    $slider_options[0]['width'],
                    $slider_options[0]['height'],
                    $upload_dir_info . "/" .$_POST['tmp_picture']
                );

                if ($slider_options[0]['deny_resize_img']) {
                    /**
                     * No resize image
                     */

                    if ($check_result['check']){
                        $file->copy_file(
                            $upload_dir_info . "/" .$_POST['tmp_picture'],
                            FULL_UPLOAD_PATH . "/" . $slider_options[0]['id'] . "/",
                            $picture_name
                        );
                    }else {
                        /**
                         * however needs correction
                         */
                        $file->resize_upload_img(
                                $upload_dir_info . "/" .$_POST['tmp_picture'],
                                $picture_name,
                                $check_result['resize_height'],
                                $check_result['resize_width'],
                                FULL_UPLOAD_PATH . "/" . $slider_options[0]['id'] . "/",
                                false
                            );

                        $file->center_place_img_to_canvas(
                            $slider_options[0]['width'],
                            $slider_options[0]['height'],
                            $check_result['frame_orient'],
                            FULL_UPLOAD_PATH . "/" . $slider_options[0]['id'] . "/" . $picture_name
                        );
                    }

                }else {
                    /**
                     * Needs resize
                     */
                    if ($check_result['check']) {
                            $file->resize_upload_img(
                            $upload_dir_info . "/" .$_POST['tmp_picture'],
                            $picture_name,
                            $slider_options[0]['height'],
                            $slider_options[0]['width'],
                            FULL_UPLOAD_PATH . "/" . $slider_options[0]['id'] . "/",
                            true);
                    }else {
                            $file->resize_upload_img(
                            $upload_dir_info . "/" .$_POST['tmp_picture'],
                            $picture_name,
                            $check_result['resize_height'],
                            $check_result['resize_width'],
                            FULL_UPLOAD_PATH . "/" . $slider_options[0]['id'] . "/",
                            false
                        );
                        $file->center_place_img_to_canvas(
                            $slider_options[0]['width'],
                            $slider_options[0]['height'],
                            $check_result['frame_orient'],
                            FULL_UPLOAD_PATH . "/" . $slider_options[0]['id'] . "/" . $picture_name
                        );
                    }

                }

                /**
                 * Update picture field
                 */
                $wpdb->update(
                    $wpdb->prefix . SLIDES_TABLE,
                    array('picture' => $picture_name),
                    array('id' => $slide_id),
                    array('%s'),
                    array( '%d' )
                );

                /**
                 * Delete old picture
                 */
                if (!empty($_POST['old_picture']) && $picture_name) {
                    $file->delete_file(FULL_UPLOAD_PATH . "/" . $slider_options[0]['id'] . "/" . $_POST['old_picture']);
                    $file->delete_file(FULL_UPLOAD_PATH_THUMB . "/" . $slider_options[0]['id'] . "/" . $_POST['old_picture']);
                    $file->delete_file(FULL_UPLOAD_PATH_ORIG . "/" . $slider_options[0]['id'] . "/" . $_POST['old_picture']);
                }

                /**
                 * Delete tmp picture
                 */
                $file->delete_file($upload_dir_info . "/" .$_POST['tmp_picture']);
                $file->delete_file($upload_dir_info . "/preview_" .$_POST['tmp_picture']);
            }

            $wp_session->s_set('errors', $error_statuses);
    }
}

function add_slides() {
    global $wpdb;
    wp_enqueue_script('annotate');
    $page_header = __('Create New Slide');

    $slider_options = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                id, deny_resize_img, width, height
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "`
                WHERE
                    id = %d
                ",
            $_GET['slider_id']
        ),
        ARRAY_A
    );

    $upload_dir_info = wp_upload_dir();

    if (isset($_GET['id'])) {
        $page_header = __('Edit Slide');
        $res = array();
        $res = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                    *
                FROM
                    `" . $wpdb->prefix . SLIDES_TABLE . "`
                WHERE
                    id = %d",
                $_GET['id']
            ),
            ARRAY_A
        );

        $required = '';

    }else {
        $required = 'required';
    }

    ?>
    <script type="application/javascript">
        var annotate;
        var response;
        var relative_url = '<?php echo get_site_url()?>';
        function InitHotspotBtn() {
            if (jQuery("img#LookbookImage")) {
                var annotObj = jQuery("img#LookbookImage").annotateImage({
                    editable: true,
                    useAjax: false,
                    interdict_areas_overlap: <?php echo (get_option('wplb_free_hotspots_overlap')) ? 'true' : 'false';?>,
                    captions: {"add_btn":"Add Hotspot","cancel_btn":"Cancel","delete_btn":"Delete","note_saving_err":"An error occurred saving this hotspot.","note_overlap_err":"Areas should not overlap.","link_text":"Link text","link_href":"Link url","enter_text_err":"Please, enter link text","enter_href_err":"Please, enter link url","link_type":"Select link type","link_required_err":"Please, enter link text and link url (with http://)","enter_sku_err":"Please, enter product ID","select_link_type_err":"Please, select link type","prod_dont_exists_err":"The product with ID=","prod_sku":"Product(post,page) ID:","delete_note_err":"An error occurred deleting this hotspot.","product_page":"Product page","other_page":"External page"},
                    notes: <?php echo (empty($res[0]['hotsposts'])) ? '[]' : $res[0]['hotsposts'];?>,
                    input_field_id: "hotspots"
                });

                var top = Math.round(jQuery("img#LookbookImage").height()/2);
                jQuery(".image-annotate-canvas").append('<div class="hotspots-msg" style="top:' + top + 'px;">Rollover on the image to see hotspots</div>');

                jQuery(".image-annotate-canvas").hover(
                    function () {
                        ShowHideHotspotsMsg();
                    },
                    function () {
                        ShowHideHotspotsMsg();
                    }
                );

                return annotObj;
            }
            else
            {
                return false;
            }
        };

        function save_notes() {
            var notes_obj = JSON.stringify(annotate.notes);
            jQuery("input[name='lb_hotsposts']").val(notes_obj);
            return true;
        }


        jQuery(document).ready(function() {

            annotate = InitHotspotBtn();

            jQuery("#lb_picture_id").on("change", function() {
                var file_data = jQuery("#lb_picture_id").prop("files")[0];
                var form_data = new FormData();

                jQuery("#upload_progress").show();

                form_data.append("lb_picture", file_data);
                form_data.append("noredirect", true);
                form_data.append("slider_id", jQuery("#slider_id").val());
                form_data.append("_alfw_nonce", jQuery("#_alfw_nonce").val());

                jQuery.ajax({
                    url: "admin.php?page=lookbook&lb_action=ajax_upload",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(data, status){
                        if (status == 'success') {
                            if (data.error) {
                                var error_text = '';
                                jQuery.each(data.msg, function(key, value) {
                                    error_text += value + ' ';
                                });
                                jQuery("#upload_results").text(error_text);
                            }else {
                                jQuery("#lb_tmp_picture").val(data.tmp_file);
                                jQuery("#lb_tmp_picture_name").val(data.file_name);
                                jQuery("#upload_results").text(data.msg[0]);
                                var tmp_img = '<img id="LookbookImage" width="<?php echo $slider_options[0]['width'];?>px" height="<?php echo $slider_options[0]['height'];?>px" src="<?php echo $upload_dir_info['baseurl'];?>/' + data.preview_file + '" />';
                                jQuery("#LookbookImageBlock").html(tmp_img);
                                annotate = InitHotspotBtn();
                            }

                        }else{
                            jQuery("#upload_results").text('File not uploaded. Network error');
                        }
                        jQuery("#upload_progress").hide();
                    }
                });
            });
        });
    </script>
    <?php

    $image = (empty($res[0]['picture'])) ? '' : '<img id="LookbookImage" width="'.$slider_options[0]['width'].'" height="'.$slider_options[0]['height'].'" src="' . $upload_dir_info['baseurl'] . '/'.UPLOAD_FOLDER_NAME.'/'.$_GET['slider_id'] . '/' . $res[0]['picture'] . '" />';

    echo
        '<div><h2>' . $page_header . '</h2>
            <form method="post" onsubmit="return save_notes();" action="admin.php?page=lookbook&lb_action=store_slide" enctype="multipart/form-data">

                <input type="hidden" name="lb_slider_id" value="'.$_GET['slider_id'].'" id="slider_id" />
                <input type="hidden" name="slide_id" value="'.@$_GET['id'].'" />
                <input type="hidden" name="old_picture" value="'.@$res[0]['picture'].'" />
                <input type="hidden" name="lb_hotsposts" value="">
                <input type="hidden" name="tmp_picture" value="" id="lb_tmp_picture">
                <input type="hidden" name="tmp_picture_name" value="" id="lb_tmp_picture_name">';

            wp_nonce_field( 'store_slide', '_alfw_nonce');

    echo
        '<table class="wp-list-table widefat fixed striped pages">

            <tr>
                <td class="label">
                    <label for="slide_name">'.__('Name').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="text" required class=" input-text" value="'.@$res[0]['name'].'" name="lb_name" id="slide_name">
                </td>
                <td class=""></td>
            </tr>

            <tr>
                <td class="label">
                    <label for="slide_caption">'.__('Caption').'</label>
                </td>
                <td class="value">
                    <textarea name="lb_caption" class="lb_textarea" id="slide_caption">'.@$res[0]['caption'].'</textarea>
                </td>
                <td class=""></td>
            </tr>

            <tr>
                <td class="label">
                    <label for="slide_order">'.__('Order').'</label>
                </td>
                <td class="value">
                    <input type="text" class=" input-text" value="'.@$res[0]['order_flag'].'" name="lb_order_flag" id="slide_order" style="width: 50px">
                </td>
                <td class=""></td>
            </tr>

            <tr>
                <td class="label">
                    <label for="slide_link">'.__('Link').'</label>
                </td>
                <td class="value">
                    <input type="url" class=" input-text" value="'.@$res[0]['link'].'" name="lb_link" id="slide_link" style="width: 75%">
                </td>
                <td class=""></td>
            </tr>

            <tr>
                <td class="label">
                    <label for="lb_status">'.__('Status').'</label>
                </td>
                <td class="value">'.
                    alfw_form_select('lb_status', @$res[0]['status'], array('0'=>__('Disabled'), '1'=>__('Enabled')), $attrbutes = array( "id"=>"lb_status")) .'
                </td>
                <td class=""></td>
            </tr>

            <tr>
                <td class="label">
                    <label for="lb_picture">'.__('Upload file').'<span class="required">*</span></label>
                </td>
                <td class="value">
                    <input type="file" ' . $required . ' name="lb_picture" id="lb_picture_id"><br>
                    <div id="upload_progress"></div><div id="upload_results" class="required"></div>
                </td>
                <td class=""></td>
            </tr>
            <tr>
                <td colspan="3">
                <div id="LookbookImageBlock">
                        ' . $image . '
                    </div>
                </td>
            </tr>
            </table>
                <div class="bottom_button">
                    <input type="submit" value="'.__('Save').'" class="button button-primary button-large">
                    <a href="admin.php?page=lookbook&lb_action=manage_slides&id='.$_GET['slider_id'].'" class="button button-primary button-large">'.__('Cancel').'</a>
                </div>
            </form>
        </div>';
}

function prepare_upload_msg() {
    global $wp_session;
    $error_msg = '';
    $error = $wp_session->s_get('errors');
    if (!empty($error)) {
        $error_msg = implode(", ", $error);
    }
    $wp_session->s_del('errors');
    return $error_msg;
}

function prepare_data($data, $prefix = 'lb_') {
    $res_data = array();
    $formats = array();
    foreach ($data as $key=>$val) {
        if (preg_match("#^" . $prefix . "(.+)$#su",$key,$matched)){

            if (is_array($val)) {
                $res_data[$matched[1]] = serialize($val);
            }else {
                $res_data[$matched[1]] = $val;
            }
            $formats[] = (is_numeric($val)) ? "%d" : "%s";
        }
    }
    return array('data'=>$res_data, 'format'=>$formats);
}

function del_slider() {

    global $url_tail, $wp_session, $wpdb;

    $error_statuses = array();

    $file = new alfw_manage_files();

    if (isset($_POST['id']) && is_numeric($_POST['id'])) {

        $wpdb->query(
            $wpdb->prepare(
                "
                DELETE FROM `" .$wpdb->prefix. SLIDES_TABLE . "`
		        WHERE slider_id = %d
		        ",
                $_POST['id']
            )
        );

        $file->delete_directory(FULL_UPLOAD_PATH . "/" . $_POST['id']);
        $file->delete_directory(FULL_UPLOAD_PATH_THUMB . "/" . $_POST['id']);


        $wpdb->query(
            $wpdb->prepare(
                "
                DELETE FROM `" . $wpdb->prefix . SLIDER_TABLE ."`
                WHERE id = %d
                ",
                $_POST['id']
            )
        );

        $error_statuses[] = __('Slider was deleted!');
    }

    $wp_session->s_set('errors', $error_statuses);
}

function del_slides() {

    if ( ! empty( $_POST ) && check_admin_referer( 'del_slides', '_alfw_nonce' ) ) {

        global $url_tail, $wp_session, $wpdb;
        $error_statuses = array();

        $file = new alfw_manage_files();

        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $res = array();
            $res = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT
                        *
                    FROM
                        `" . $wpdb->prefix . SLIDES_TABLE . "`
                    WHERE
                        id = %d",
                    $_POST['id']
                ),
                ARRAY_A
            );

            if ($wpdb->query(
                $wpdb->prepare(
                    "
                    DELETE FROM `" . $wpdb->prefix. SLIDES_TABLE . "`
                    WHERE id = %d
                    ",
                    $_POST['id']
                )
            )){
                $file->delete_file(FULL_UPLOAD_PATH . "/" . $_POST['slider_id'] . "/" . $res[0]['picture']);
                $file->delete_file(FULL_UPLOAD_PATH_THUMB . "/" . $_POST['slider_id'] . "/" . $res[0]['picture']);
                $file->delete_file(FULL_UPLOAD_PATH_ORIG . "/" . $_POST['slider_id'] . "/" . $res[0]['picture']);
                $error_statuses[] = __('Slide was deleted!');
            }else {
                $error_statuses[] = __('Slide was not deleted!');
            }
        }
        $wp_session->s_set('errors', $error_statuses);
    }
}

function check_post_id() {
    global $wpdb;
    $res = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                ID
            FROM
                `" . $wpdb->prefix . "posts" . "`
                WHERE
                    id = %d AND post_type IN ('page', 'post', 'product', 'product_variation') ",
            $_POST['post_id']
        ),
        ARRAY_A
    );

    echo (isset($res[0]['ID']) && is_numeric($res[0]['ID'])) ? 1 : __('dosn\'t exist');
    exit();
}

function view_slider() {
    global $wpdb;

    wp_enqueue_script('hotspots');

    $slider = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                slider.name
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "` AS slider
            WHERE
                    slider.id = %d",
            $_GET['id']
        ),
        ARRAY_A
    );
    echo '<div>
            <div class="wrap">
                <h1>' . __('Slider') . ' [' . $slider[0]['name'] . '] ' . __('preview') . '
                    <a href="admin.php?page=lookbook" class="page-title-action">Back</a>
                </h1>
            </div>
            ';
    echo '<div style="width: 75%">';
    echo do_shortcode('[slider_render slider_id="'.$_GET['id'].'" admin=true]');
    echo '</div>';
    echo '</div>';
}

function ajax_upload() {

    if ( ! empty( $_POST ) && check_admin_referer( 'store_slide', '_alfw_nonce' ) ) {

        global $wpdb;

        $file = new alfw_manage_files();
        /**
         * Max file size and file extensions
         */
        $restrictions = array(
            'size' => get_option('wplb_free_max_file_size'),
            'ext' => get_option('wplb_free_allow_ext')
        );
        $check_result = alfw_check_slider_image_restriction($_FILES['lb_picture'], $restrictions);

        if ($check_result['error']) {
            echo json_encode(array('error'=>true, 'msg'=>$check_result['error_msg']));
        }else {

            /**
             * Get slider options
             */
            $slider_options = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT
                        id, deny_resize_img, width, height, thumb_width, thumb_height
                    FROM
                        `" . $wpdb->prefix . SLIDER_TABLE . "`
                    WHERE
                        id = %d
                    ",
                    $_POST['slider_id']
                ),
                ARRAY_A
            );

            $upload_dir_info = wp_upload_dir();

            preg_match("#\.(\w+)$#siu", $_FILES['lb_picture']['name'], $matches);
            $tmp_file_name = time().$matches[0];
            $tmp_file_name_preview = 'preview_' . $tmp_file_name;

            /**
             * Check orientation
             */
            $check_result = $file->check_orientation(
                $slider_options[0]['width'],
                $slider_options[0]['height'],
                $_FILES['lb_picture']['tmp_name']
            );

            if ($slider_options[0]['deny_resize_img']) {
                /**
                 * No resize image
                 */

                if ($check_result['check']){
                    $file->copy_file(
                        $_FILES['lb_picture']['tmp_name'],
                        $upload_dir_info['basedir'] . "/",
                        $tmp_file_name_preview
                    );
                }else {
                    /**
                     * however needs correction
                     */
                    $file->resize_upload_img(
                        $_FILES['lb_picture']['tmp_name'],
                        $tmp_file_name_preview,
                        $check_result['resize_height'],
                        $check_result['resize_width'],
                        $upload_dir_info['basedir'] . "/",
                        false
                    );

                    $file->center_place_img_to_canvas(
                        $slider_options[0]['width'],
                        $slider_options[0]['height'],
                        $check_result['frame_orient'],
                        $upload_dir_info['basedir'] . "/" . $tmp_file_name_preview
                    );
                }

            }else {
                /**
                 * Needs resize
                 */
                if ($check_result['check']) {
                    $file->resize_upload_img(
                        $_FILES['lb_picture']['tmp_name'],
                        $tmp_file_name_preview,
                        $slider_options[0]['height'],
                        $slider_options[0]['width'],
                        $upload_dir_info['basedir'] . "/",
                        true);
                }else {
                    $file->resize_upload_img(
                        $_FILES['lb_picture']['tmp_name'],
                        $tmp_file_name_preview,
                        $check_result['resize_height'],
                        $check_result['resize_width'],
                        $upload_dir_info['basedir'] . "/",
                        false
                    );
                    $file->center_place_img_to_canvas(
                        $slider_options[0]['width'],
                        $slider_options[0]['height'],
                        $check_result['frame_orient'],
                        $upload_dir_info['basedir'] . "/" . $tmp_file_name_preview
                    );
                }

            }

            /**
             *  Original picture
             */
            if ($picture = $file->upload_file(
                $_FILES['lb_picture']['tmp_name'],
                $upload_dir_info['basedir'] . "/",
                $tmp_file_name
                )
            ){
                echo json_encode(array('error'=>false, 'msg'=>array('File uploaded'), 'tmp_file'=>$picture, 'file_name'=>$_FILES['lb_picture']['name'], 'preview_file'=>$tmp_file_name_preview));
            }else {
                echo json_encode(array('error'=>true, 'msg'=>array('File not uploaded')));
            }
        }
    }
    exit();
}

/**
 * @param int $slider_id
 * @param string $picture_folder_name
 * @param array $dimension_keys - possible keys width, height
 */
function packet_resize($slider_id, $picture_folder_name, $dimension_keys = array()){
    global $wpdb;
    $file = new alfw_manage_files();

    /**
     * Get slider options
     */
    $slider_options = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                id, deny_resize_img, width, height, thumb_width, thumb_height
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "`
                WHERE
                    id = %d
                ",
            $slider_id
        ),
        ARRAY_A
    );

    $upload_dir_info = wp_upload_dir();
    $uploads_folder_path = $upload_dir_info['basedir'];
    $path_to_resize_images = $uploads_folder_path . "/" . $picture_folder_name . "/" . $slider_id . "/";
    $pictures = $file->enum_directory_files( FULL_UPLOAD_PATH_ORIG . "/" . $slider_id . "/" );

    foreach ($pictures as $picture) {

            /**
             * Check orientation
             */
            $check_result = $file->check_orientation(
                $slider_options[0][$dimension_keys['width']],
                $slider_options[0][$dimension_keys['height']],
                FULL_UPLOAD_PATH_ORIG . "/" . $slider_id . "/" . $picture
            );

        if ($slider_options[0]['deny_resize_img']) {
            /**
             * No resize image
             */

            if ($check_result['check']) {
                if ($picture_folder_name == UPLOAD_FOLDER_NAME_THUMB){
                    $file->resize_upload_img(
                        FULL_UPLOAD_PATH_ORIG . "/" . $slider_id . "/" . $picture,
                        $picture,
                        $slider_options[0][$dimension_keys['height']],
                        $slider_options[0][$dimension_keys['width']],
                        $path_to_resize_images,
                        true,
                        false);
                }else{
                    $file->copy_file(
                        FULL_UPLOAD_PATH_ORIG . "/" . $slider_id . "/" . $picture,
                        $path_to_resize_images,
                        $picture,
                        false
                    );
                }
            }else {
                /**
                 * however needs correction (for diff orientation)
                 */
                $file->resize_upload_img(
                    FULL_UPLOAD_PATH_ORIG . "/" . $slider_id . "/" . $picture,
                    $picture,
                    $check_result['resize_height'],
                    $check_result['resize_width'],
                    $path_to_resize_images,
                    false,
                    false
                );

                $file->center_place_img_to_canvas(
                    $slider_options[0][$dimension_keys['width']],
                    $slider_options[0][$dimension_keys['height']],
                    $check_result['frame_orient'],
                    $path_to_resize_images . $picture
                );
            }

        }else {
            /**
             * Needs resize
             */
            if ($check_result['check']) {
                $file->resize_upload_img(
                    FULL_UPLOAD_PATH_ORIG . "/" . $slider_id . "/" . $picture,
                    $picture,
                    $slider_options[0][$dimension_keys['height']],
                    $slider_options[0][$dimension_keys['width']],
                    $path_to_resize_images,
                    true,
                    false);
            }else {
                $file->resize_upload_img(
                    FULL_UPLOAD_PATH_ORIG . "/" . $slider_id . "/" . $picture,
                    $picture,
                    $check_result['resize_height'],
                    $check_result['resize_width'],
                    $path_to_resize_images,
                    false,
                    false
                );
                $file->center_place_img_to_canvas(
                    $slider_options[0][$dimension_keys['width']],
                    $slider_options[0][$dimension_keys['height']],
                    $check_result['frame_orient'],
                    $path_to_resize_images . $picture
                );
            }
        }
    }
}

function store_options() {
    if ( ! empty( $_POST ) && check_admin_referer( 'store_options', '_alfw_nonce' ) ) {
        /**
         * Storing plugin options
         */
        $look_book_options = explode(",", $_POST['page_options']);
        foreach ($look_book_options as $option){
            if (isset($_POST[$option])){
                update_option( $option, $_POST[$option] );
            }
        }

        /**
         * Storing slider option
         */
        store_slider();
    }
    $site_url = esc_url( home_url( '/' ) );
    wp_redirect($site_url . "wp-admin/admin.php?page=lookbook");
    exit;
}

function update2prof_notice() {
    echo '<div id="wp-lb-free-notice" class="updated notice my-acf-notice is-dismissible">
            <p>Now Professional version of Altima Lookbook for WooCommerce available. Unlimited number of sliders, slides and hotspots, priority support.
            Visit <a target="_blank" href="https://shop.altima.net.au/woocommerce-lookbook-professional.html">Altima Lookbook Pro for WooCommerce</a> to purchase.</p>
         </div>';
}

$update2prof_notice = get_option('update2prof_notice');

if( !function_exists( 'the_field' ) && empty( $update2prof_notice ) ) {
    add_action('admin_notices', 'update2prof_notice');
}

add_action('wp_ajax_wplookbook_free_dismiss_acf_notice', 'wplookbook_free_dismiss_acf_notice');

function wplookbook_free_dismiss_acf_notice() {
    update_option('update2prof_notice', 1);
}

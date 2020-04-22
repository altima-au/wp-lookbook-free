<?php

add_filter( 'woocommerce_get_price_html', function( $price, $product )
{
    global $woocommerce_loop;

    // check if we are in single product page, in main section, and if product has price and is on sale
    if ( is_product() && !isset( $woocommerce_loop ) && $product->get_price() && $product->is_on_sale() ) {

        // collect prices from $price html string
        $prices = array_map( function( $item ) {
            return array( $item, (float) preg_replace( "/[^0-9.]/", "", html_entity_decode( $item, ENT_QUOTES, 'UTF-8' ) ) );
        }, explode( ' ', strip_tags( $price ) ) );

        $price = isset( $prices[0][0] ) ? '<span class="orig-price">Original Price: ' . $prices[0][0] . '</span>' : '';
        $price .= isset( $prices[1][0] ) ? '<span class="sale-price">Sale Price: ' . $prices[1][0] . '</span>' : '';

        if ( $product->get_regular_price() ) {
            // set saved amount with currency symbol placed as defined in options
            $price .= '<span class="saved">You saved: ' . sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $prices[0][1] - $prices[1][1] ) . '</span>';
        }
    }

    return $price;

}, 10, 2 );

add_action ('check_woocommerce', 'alfw_woocommerce_not_install_notice');

/**
 * @param $atts
 */
function alfw_slider_render_func($atts) {

    global $wpdb, $lookbook_slider_effects;
    $output = '';

    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        do_action( 'check_woocommerce');
        return;
    }

    $upload_dir_info = wp_upload_dir();
    $wp_root_path = get_home_path();
    $site_url = get_site_url();
    $plugin_folder_name = pathinfo(ALTIMA_LOOKBOOK_PLUGIN_DIR);

    wp_enqueue_script('hotspots');
    wp_enqueue_script('actual');
    wp_enqueue_script('carousel');
    wp_enqueue_script('flip');
    wp_enqueue_script('scrolV');
    wp_enqueue_script('shuffle');
    wp_enqueue_script('tile');
    wp_enqueue_script('additionalEffect');
    wp_enqueue_script('swipe');

    //$wpdb->show_errors();
    extract($atts, EXTR_OVERWRITE);

    /**
     * Get slider options
     */
    $show_desc = get_option("wplb_free_show_desc_in_popup");
    $show_addcart = get_option("wplb_free_show_addcart_in_popup");

    $path_2_pic = get_option('wplb_free_hspt_icon', $site_url . '/wp-content/plugins/'.$plugin_folder_name['basename'].'/admin/images/hotspot-icon.png');

    if (empty($path_2_pic)) {
        $path_2_pic = $site_url . '/wp-content/plugins/'.$plugin_folder_name['basename'].'/admin/images/hotspot-icon.png';
    }

    $hot_point_icon = '<img class="hotspot-icon" src="' . $path_2_pic . '" />';

    $only_visible_element = 'AND `status`=1';

    if (@$admin)
        $only_visible_element = '';

    $slider = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                slider.*
            FROM
                `" . $wpdb->prefix . SLIDER_TABLE . "` AS slider
            WHERE
                    `slider`.`id` = %d $only_visible_element",
            $slider_id
        ),
        ARRAY_A
    );


    if (!empty($slider)) {
        $slides = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                    slide.*
                FROM
                    `" . $wpdb->prefix . SLIDES_TABLE . "` AS slide
                WHERE
                    slider_id = %d $only_visible_element
                ORDER BY
                    order_flag",
                $slider[0]['id']
            ),
            ARRAY_A
        );
    }
    if (empty($slides)){
        return __('No slides available in this slider!');
    }else {
        $effects = unserialize($slider[0]['slider_effect']);
        $slider_count = count($slides);

        //var_dump(json_decode($slides[0]['hotsposts']));
        alfw_control_js($slider[0]['id']);
        /**
         * Work with sliders effect
         */
        if (!empty($effects)){

            $effects_string = '';

            if (in_array('all', $effects)) {
                unset($lookbook_slider_effects['all']);
                unset($lookbook_slider_effects['none']);
                $fx_names = array_keys($lookbook_slider_effects);
                $fx_count = count($fx_names);
                $sl_count = $slider_count;
                for ($i = 0; $i < $sl_count; $i++) {
                    $slides[$i]['fx'] = 'data-cycle-fx="'.$fx_names[rand(2,$fx_count)].'"';
                }
            }elseif (in_array('none', $effects)) {
                $effects_string = 'data-cycle-fx="none"';
            }else {
                $fx_count = count($effects)-1;
                $sl_count = $slider_count;
                for ($i=0; $i<$sl_count; $i++) {
                    $slides[$i]['fx'] = 'data-cycle-fx="'.$effects[rand(0,$fx_count)].'"';
                }
            }
        }

        $output = '<div class="lb_conteyner">';

        /**
         * Output content before
         */
        if (!empty($slider[0]['content_before'])) {
            $output .= '<div class="c_before">'.$slider[0]['content_before'].'</div>';
        }

        /**
         * Showing the caption
         */
        $caption_plugin = '';
        if ($slider[0]['show_slide_caption']){
            $caption_plugin = 'data-cycle-caption-plugin="caption2"';
        }

        /**
         * Adding thumbneil pager
         */
        $thumb = '';
        $simple_pager = '';

        if ($slider[0]['show_thumbnails']){
            $thumb = 'data-cycle-pager="#no-template-pager"
            data-cycle-pager-template=""';
        }else{
            $simple_pager ='
                data-cycle-pager="#pagernav_' . $slider[0]['id'] . ' .cycle"
                data-cycle-pager-template="<li><span> {{slideNum}} </span></li>"';
        }

        /**
         * Output the slider
         * cycle-slideshow
         */
        $output .= '<div
            id="lookbookslider_'.$slider[0]['id'].'"
            class="cycle-slideshow"
            data-cycle-speed="'.$slider[0]['transition_duration'].'"
            data-cycle-timeout="'.$slider[0]['pause'].'"
            data-cycle-slides="> div.slide"
            ' . $effects_string . '
            ' . $caption_plugin . '
            ' . $thumb . '
            ' . $simple_pager . '
            data-cycle-next="> .slide-next"
            data-cycle-prev="> .slide-prev"
            data-cycle-log="false"
            data-cycle-auto-height="container"
            data-cycle-swipe=true
            data-cycle-swipe-fx=scrollHorz
            >';

        /**
         * Showing the caption
         */
        if ($slider_count > 1) {
            if ($slider[0]['show_slide_caption']) {
                $output .= '<div class="cycle-caption"></div>';
                //echo '<div class="cycle-overlay"></div>';
            }
        }

        /**
         * Showing nawigation buttons
         */
        if ($slider[0]['show_navigation'] && ($slider_count > 1)) {
            $hover = '';
            if ($slider[0]['navigation_on_hover_state_only']){
                $hover = 'hover';
            }
            $output .= '
            <div class="slide_commands ' . $hover . '">
                <div class="slide_play" style="display: none;"></div>
                <div class="slide_stop" style="display: block;"></div>
            </div>';
            $output .= '<div class="slide-prev ' . $hover . '"><span></span></div>';
            $output .= '<div class="slide-next ' . $hover . '"><span></span></div>';
        }

        foreach($slides as &$slide) {

            /**
             * Add to hotspot product information
             */
                $hotspots = json_decode($slide['hotsposts']);

                if (!empty($hotspots)) {

                    foreach ($hotspots as &$point) {

                        /**
                         * $point->sku - it's post id not product sku
                         */

                        if (!empty($point->sku) && is_numeric($point->sku)){

                            $post_data = get_post($point->sku, ARRAY_A);
                            try {
                                $product = new WC_Product( $post_data['ID'] );
                                $price = $product->get_price_html();
                                $stock_status = $product->is_in_stock() ? 'In stock' : 'Out of stock';
                                $product_url = get_permalink($post_data['ID']);

                                $url = wp_get_attachment_image_src( get_post_thumbnail_id($post_data['ID']));

                                $status_block = '';
                                $price_block = '';
                                $text_block = '';
                                $addcart_block = '';
                                $img_block = '';

                                if ($show_desc) {
                                    $img_block = (!empty($url[0])) ? '<img src="' . $url[0] . '" style="width:50px" />' : '';
                                }

                                switch($post_data['post_type']){
                                    case 'post':
                                        if ($show_desc) {
                                            $text_block = mb_substr(strip_tags($post_data['post_content']), 0, 100);
                                        }
                                    break;
                                    case 'page':
                                        if ($show_desc) {
                                            $text_block = mb_substr(strip_tags($post_data['post_content']), 0, 100);
                                        }
                                    break;
                                    case 'product':
                                        $status_block = '<div class="out-of-stock"><span>' . $stock_status . '</span></div>';
                                        $price_block = '<div class="price">'.$price.'</div>';

                                        if ($show_desc) {
                                            $text_block = mb_substr(strip_tags($post_data['post_excerpt']), 0, 100);
                                        }

                                        if ($show_addcart) {
                                            $addcart_block =
                                            '<div class="add-to-cart">
                                                <form method="post" action="'. esc_url($product_url). '">
                                                    <input type="hidden" value="' . $post_data['ID'] . '" name="add-to-cart">
                                                    <label for="qty">' . __('Qty') . ':</label>
                                                    <input type="text" class="qty" title="Qty" value="1" maxlength="12" id="qty" name="quantity">
                                                    <button class="button btn-cart" title="Add to Cart" type="submit"><span>' . __('Add to Cart') . '</span></button>
                                                </form>
                                            </div>';
                                        }
                                    break;

                                }

                                $point->text =
                                '<div class="product-info">
                                    <div class="pro-detail-div">
                                        <div class="left-detail">
                                            <a href="' . $product_url .'">' . $post_data['post_title'] . '</a>
                                            ' . $status_block . '
                                            <div class="desc">
                                                ' . $img_block . '
                                                ' . $text_block . '
                                            </div>
                                            ' . $price_block . '
                                            ' . $addcart_block . '
                                        </div>
                                    </div>
                                </div>' . $hot_point_icon;
                            }catch (Exception $e) {
                                    $point->text = '<div class="product-info">
                                                <div class="pro-detail-div">
                                                    <div>
                                                        <a href="#">' . $e->getMessage() . '</a>
                                                    </div>
                                                </div>
                                            </div>' . $hot_point_icon;
                            }
                        }else {
                            $point->text = '<div class="product-info">
                                                <div class="pro-detail-div">
                                                    <div>
                                                        <a href="'.$point->href.'" target="_blank">'.$point->text.'</a>
                                                    </div>
                                                </div>
                                            </div>' . $hot_point_icon;
                        }
                    }

                    $slide['hotsposts'] = json_encode($hotspots);
                }

            /**
             * Output slides
             */
            $a_start = $a_end = '';
            if (!empty($slide['link'])){
                $a_start = '<a href="' . $slide['link'] . '">';
                $a_end = '</a>';
            }

            $overley = '';
            if ($slider_count > 1) {
                if ($slider[0]['show_slide_caption']) {
                    $overley = '<div class="cycle-overlay">'.$slide['caption'].'&nbsp;</div>';
                }
            }

            $output .= '<div class="slide" id="s_img_'.$slide['id'].'" '.$slide['fx'].' data-cycle-desc="'.$slide['caption'].'">
                    ' . $a_start . '<img src="'.$upload_dir_info['baseurl'].'/' . UPLOAD_FOLDER_NAME . '/' . $slider_id . '/' . $slide['picture'].'" alt="'.$slide['caption'].'" />' . $overley .  $a_end . '
                 </div>'."\n";


        }

        $output .= '<div id="progress_' . $slider[0]['id'] . '"></div>';

        unset($slide);

        $output .= '</div>';

        alfw_add_hotspots($slides, $slider[0]['id']);

        /**
         * Showing thumbneils pages
         */
        if ( $slider_count > 1) {
            if ($slider[0]['show_thumbnails']) {
                alfw_thumbnails_js($slider[0]['id']);
                $output .=
                '<div id="pagernav_' . $slider[0]['id'] . '" class="pagernav" style="max-width: ' . $slider[0]['width'] . 'px;">
                    <ul id="thumb_nav" class="cycle-slideshow"
                        data-cycle-slides="> li.thumb"
                        data-cycle-timeout="0"
                        data-cycle-fx="carousel"
                        data-cycle-carousel-visible="'.$slider_count.'"
                        data-cycle-carousel-fluid=true
                        data-allow-wrap="false"
                        data-cycle-log="false"
                        data-cycle-prev="#pagernav_'.$slider[0]['id'].' .cycle-prev"
                        data-cycle-next="#pagernav_'.$slider[0]['id'].' .cycle-next"
                    >';

                    foreach($slides as $slide){
                        $output .= '<li class="thumb">
                                <img src="'.$upload_dir_info['baseurl']. '/' . UPLOAD_FOLDER_NAME_THUMB . '/' . $slider_id . '/' . $slide['picture'].'" alt="'.$slide['caption'].'" />
                             </li>'."\n";
                    }
                $output .=
                    '</ul>
                </div>';
            }else {
                /**
                 * Simple pager
                 */
                $output .= '
                <div class="pagernav" id="pagernav_' . $slider[0]['id'] . '">
                    <ul class="cycle">
                    </ul>
                </div>';
            }
        }

        /**
         * Output content after
         */
        if (!empty($slider[0]['content_after'])) {
            $output .= '<div class="c_after">'.$slider[0]['content_after'].'</div>';
        }

        $output .= '</div>';
    }

    return $output;
}

add_shortcode( 'slider_render', 'alfw_slider_render_func');

function alfw_add_hotspots($slides, $slider_id) {

    foreach ($slides as $slide):
        $hotspots[] = json_decode($slide['hotsposts']);
    endforeach;

    wp_register_script(
        'alfw_hotspots',
        ALTIMA_LOOKBOOK_PLUGIN_URL . '/assets/js/alfw_hotspots.js',
        array(),
        1.0,
        true
    );

    wp_enqueue_script( 'alfw_hotspots' );

    $script_params = array(
        'slider_id' => $slider_id,
        'hotspots' => $hotspots
    );

    wp_localize_script( 'alfw_hotspots', 'hotspotParams_' . $slider_id, $script_params );
}

function alfw_thumbnails_js($slider_id) {
    wp_register_script(
        'alfw_thumbnails',
        ALTIMA_LOOKBOOK_PLUGIN_URL . '/assets/js/alfw_thumbnails.js',
        array( /* dependencies*/ ),
        1.0,
        true
    );

    wp_enqueue_script( 'alfw_thumbnails' );

    $script_params = array(
        'slider_id' => $slider_id
    );
    wp_localize_script( 'alfw_thumbnails', 'thumbnailsParams_' . $slider_id, $script_params );
}

function alfw_control_js($slider_id) {
    wp_register_script(
        'alfw_control',
        ALTIMA_LOOKBOOK_PLUGIN_URL . '/assets/js/alfw_control.js',
        array( /* dependencies*/ ),
        1.0,
        true
    );

    wp_enqueue_script( 'alfw_control' );

    $script_params = array(
        'slider_id' => $slider_id
    );

    wp_localize_script( 'alfw_control', 'scriptParams_' . $slider_id, $script_params );
}
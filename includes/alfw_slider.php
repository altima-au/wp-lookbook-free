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

    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        do_action( 'check_woocommerce');
        return;
    }

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
    extract($atts);

    /**
     * Get slider options
     */
    $show_desc = get_option("wplb_free_show_desc_in_popup");
    $show_addcart = get_option("wplb_free_show_addcart_in_popup");

    $path_2_pic = get_option('wplb_free_hspt_icon', '/wp-content/plugins/lookbook-free/admin/images/hotspot-icon.png');
    if (empty($path_2_pic)) {
        $path_2_pic = '/wp-content/plugins/lookbook-free/admin/images/hotspot-icon.png';
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
        echo __('No slides available in this slider!');
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

        echo '<div class="lb_conteyner">';

        /**
         * Output content before
         */
        if (!empty($slider[0]['content_before'])) {
            echo '<div class="c_before">'.$slider[0]['content_before'].'</div>';
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
        echo '<div
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
                echo '<div class="cycle-caption"></div>';
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
            echo '
            <div class="slide_commands ' . $hover . '">
                <div class="slide_play" style="display: none;"></div>
                <div class="slide_stop" style="display: block;"></div>
            </div>';
            echo '<div class="slide-prev ' . $hover . '"><span></span></div>';
            echo '<div class="slide-next ' . $hover . '"><span></span></div>';
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

            echo '<div class="slide" id="s_img_'.$slide['id'].'" '.$slide['fx'].' data-cycle-desc="'.$slide['caption'].'">
                    ' . $a_start . '<img src="/wp-content/uploads/' . UPLOAD_FOLDER_NAME . '/' . $slider_id . '/' . $slide['picture'].'" alt="'.$slide['caption'].'" />' . $overley .  $a_end . '
                 </div>'."\n";


        }

        echo '<div id="progress_' . $slider[0]['id'] . '"></div>';

        unset($slide);

        echo '</div>';

        alfw_add_hotspots($slides, $slider[0]['id']);

        /**
         * Showing thumbneils pages
         */
        if ( $slider_count > 1) {
            if ($slider[0]['show_thumbnails']) {
                alfw_thumbnails_js($slider[0]['id']);
                echo
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
                        echo '<li class="thumb">
                                <img src="/wp-content/uploads/' . UPLOAD_FOLDER_NAME_THUMB . '/' . $slider_id . '/' . $slide['picture'].'" alt="'.$slide['caption'].'" />
                             </li>'."\n";
                    }
                echo
                    '</ul>
                </div>';
            }else {
                /**
                 * Simple pager
                 */
                echo '
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
            echo '<div class="c_after">'.$slider[0]['content_after'].'</div>';
        }

        echo '</div>';
    }
}

add_shortcode( 'slider_render', 'alfw_slider_render_func');

function alfw_add_hotspots($slides, $slider_id) {
    foreach ($slides as $slide):
        $hotspots[] = $slide['hotsposts'];
    endforeach;
    ?>
    <script type="application/javascript">
        var $altima_jq = jQuery.noConflict();
        $altima_jq(document).ready(function() {
            var hotspots_<?php echo $slider_id;?> = [<?php echo implode(",",$hotspots)?>];
            $altima_jq('#lookbookslider_<?php echo $slider_id;?> [id^=s_img_]').each(function (i) {
                    var ind = $altima_jq(this).index();
                    var slide = $altima_jq(this);
                    //console.log(hotspots[i]);
                    //console.log(slide);
                    $altima_jq.setHotspots(slide, hotspots_<?php echo $slider_id;?>[i]);
                });
            <?php
            /**
             * Disable hovers for slide navigation
             */
                if (count($slides) <= 1) {
            ?>
                $altima_jq( ".cycle-slideshow" ).unbind('mouseenter mouseleave');
            <?php
                }
            ?>
        });
    </script>
    <?php
}

function alfw_thumbnails_js($slider_id) {
    ?>
    <script type="application/javascript">

        var $altima_jq = jQuery.noConflict();

        $altima_jq(document).ready(function() {

            slideshow = $altima_jq( '.cycle-slideshow' );

            $altima_jq('#pagernav_<?php echo $slider_id;?> ul li').click(function () {
                var index = $altima_jq('#pagernav_<?php echo $slider_id;?> ul').data('cycle.API').getSlideIndex(this);
                $altima_jq('#lookbookslider_<?php echo $slider_id;?>').cycle('goto', index);
                $altima_jq('#pagernav_<?php echo $slider_id;?> ul').cycle('goto', index);
            });

            slideshow.on( 'cycle-before', function( event, optionHash, outgoingSlideEl, incomingSlideEl, forwardFlag ) {
                var index = $altima_jq('#lookbookslider_<?php echo $slider_id;?>').data('cycle.API').getSlideIndex(incomingSlideEl);
                $altima_jq('#pagernav_<?php echo $slider_id;?> ul').cycle('goto', index);
            });

        });
    </script>
<?php
}

function alfw_control_js($slider_id) {
    ?>
    <script type="application/javascript">

        var altima_jq = jQuery.noConflict();

        altima_jq(document).ready(function() {

            var slide_control_<?php echo $slider_id;?> = false;

            altima_jq( "#lookbookslider_<?php echo $slider_id;?>").not('ul').hover(
                function() {
                    altima_jq('#lookbookslider_<?php echo $slider_id;?>').not('ul').cycle('pause');
                }, function() {
                    if (!slide_control_<?php echo $slider_id;?>)
                        altima_jq('#lookbookslider_<?php echo $slider_id;?>').not('ul').cycle('resume');
                }
            );


            altima_jq('#lookbookslider_<?php echo $slider_id;?> .slide_stop').click(function () {
                altima_jq('#lookbookslider_<?php echo $slider_id;?>').not('ul').cycle('pause');
                altima_jq('#lookbookslider_<?php echo $slider_id;?> .slide_stop').hide();
                altima_jq('#lookbookslider_<?php echo $slider_id;?> .slide_play').show();
                slide_control_<?php echo $slider_id;?> = true;
            });

            altima_jq('#lookbookslider_<?php echo $slider_id;?> .slide_play').click(function () {
                altima_jq('#lookbookslider_<?php echo $slider_id;?>').cycle('resume');
                altima_jq('#lookbookslider_<?php echo $slider_id;?> .slide_play').hide();
                altima_jq('#lookbookslider_<?php echo $slider_id;?> .slide_stop').show();
                slide_control_<?php echo $slider_id;?> = false;
            });


            var progress_<?php echo $slider_id;?> = altima_jq('#progress_<?php echo $slider_id;?>'),
                slideshow_<?php echo $slider_id;?> = altima_jq('#lookbookslider_<?php echo $slider_id;?>' );

            altima_jq('#progress_<?php echo $slider_id;?>').css({"position": "absolute", "bottom": "0", "height": "6px", "width": "0px", "background" : "#ffb900", "z-index" : "500"});

            slideshow_<?php echo $slider_id;?>.on( 'cycle-initialized cycle-before', function( e, opts ) {
                progress_<?php echo $slider_id;?>.stop(true).css( 'width', 0 );
            });

            slideshow_<?php echo $slider_id;?>.on( 'cycle-initialized cycle-after', function( e, opts ) {
                if ( ! slideshow_<?php echo $slider_id;?>.is('.cycle-paused') )
                    progress_<?php echo $slider_id;?>.animate({ width: '100%' }, opts.timeout, 'linear' );
            });

            slideshow_<?php echo $slider_id;?>.on( 'cycle-paused', function( e, opts ) {
                progress_<?php echo $slider_id;?>.stop();
            });

            slideshow_<?php echo $slider_id;?>.on( 'cycle-resumed', function( e, opts, timeoutRemaining ) {
                progress_<?php echo $slider_id;?>.animate({ width: '100%' }, timeoutRemaining, 'linear' );
            });

        });
    </script>
    <?php
}
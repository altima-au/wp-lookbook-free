var $altima_jq = jQuery.noConflict();

$altima_jq(document).ready(function() {

    let hotspotParamsFunc = [];
    /**
     * Workaround for multiple slider on the same page
     */
    for(var b in window) {
        if(window.hasOwnProperty(b)){
            if (b.match(/hotspotParams_/)){
                hotspotParamsFunc.push( eval(b) );
            }
        }
    }

    for (let slider of hotspotParamsFunc) {

        let slider_id = slider.slider_id;
        let lookbook_css_id = '#lookbookslider_' + slider_id;

        $altima_jq(lookbook_css_id + ' [id^=s_img_]').each(function (i) {
            var ind = $altima_jq(this).index();
            var slide = $altima_jq(this);
            //console.log(slider.hotspots[i]);
            //console.log(slide);
            $altima_jq.setHotspots(slide, slider.hotspots[i]);
        });

        /**
         * Disable hovers for slide navigation
         */
        if (slider.hotspots.length <= 1) {
            $altima_jq(".cycle-slideshow").unbind('mouseenter mouseleave');
        }

    }
});
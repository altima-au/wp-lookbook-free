var altima_jq = jQuery.noConflict();

altima_jq(document).ready(function() {

    let scriptParamsFunc = [];
    /**
     * Workaround for multiple slider on the same page
     */
    for(var b in window) {
        if(window.hasOwnProperty(b)){
            if (b.match(/scriptParams_/)){
                scriptParamsFunc.push(eval(b));
            }
        }
    }

    for (let slider of scriptParamsFunc)
    {
        let slider_id = slider.slider_id;
        let lookbook_css_id = '#lookbookslider_' + slider_id;
        let lookbook_progress_css_id = '#progress_' + slider_id;

        let slide_control = [];
        slide_control[slider_id] = false;

        altima_jq(lookbook_css_id).not('ul').hover(
            function () {
                altima_jq(lookbook_css_id).not('ul').cycle('pause');
            }, function () {
                if (!slide_control[slider_id])
                    altima_jq(lookbook_css_id).not('ul').cycle('resume');
            }
        );


        altima_jq(lookbook_css_id + ' .slide_stop').click(function () {
            altima_jq(lookbook_css_id).not('ul').cycle('pause');
            altima_jq(lookbook_css_id + ' .slide_stop').hide();
            altima_jq(lookbook_css_id + ' .slide_play').show();
            slide_control[slider_id] = true;
        });

        altima_jq(lookbook_css_id + ' .slide_play').click(function () {
            altima_jq(lookbook_css_id).cycle('resume');
            altima_jq(lookbook_css_id + ' .slide_play').hide();
            altima_jq(lookbook_css_id + ' .slide_stop').show();
            slide_control[slider_id] = false;
        });

        let progress = [];
        let slideshow = [];

        progress[slider_id] = altima_jq(lookbook_progress_css_id), slideshow[slider_id] = altima_jq(lookbook_css_id);

        altima_jq(lookbook_progress_css_id).css({
            "position": "absolute",
            "bottom": "0",
            "height": "6px",
            "width": "0px",
            "background": "#ffb900",
            "z-index": "500"
        });

        slideshow[slider_id].on('cycle-initialized cycle-before', function (e, opts) {
            progress[slider_id].stop(true).css('width', 0);
        });

        slideshow[slider_id].on('cycle-initialized cycle-after', function (e, opts) {
            if (!slideshow[slider_id].is('.cycle-paused'))
                progress[slider_id].animate({width: '100%'}, opts.timeout, 'linear');
        });

        slideshow[slider_id].on('cycle-paused', function (e, opts) {
            progress[slider_id].stop();
        });

        slideshow[slider_id].on('cycle-resumed', function (e, opts, timeoutRemaining) {
            progress[slider_id].animate({width: '100%'}, timeoutRemaining, 'linear');
        });
    }
});
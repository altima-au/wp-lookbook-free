var $altima_jq = jQuery.noConflict();

$altima_jq(document).ready(function() {

    let thumbnailsParamsFunc = [];

    /**
     * Workaround for multiple slider on the same page
     */
    for(var b in window) {
        if(window.hasOwnProperty(b)){
            if (b.match(/thumbnailsParams_/)){
                thumbnailsParamsFunc.push(eval(b));
            }
        }
    }

    for (let slider of thumbnailsParamsFunc) {

        let slider_id = slider.slider_id;

        slideshow = $altima_jq('.cycle-slideshow');

        $altima_jq('#pagernav_' + slider_id + ' ul li').click(function () {
            var index = $altima_jq('#pagernav_' + slider_id + ' ul').data('cycle.API').getSlideIndex(this);
            $altima_jq('#lookbookslider_' + slider_id).cycle('goto', index);
            $altima_jq('#pagernav_' + slider_id + ' ul').cycle('goto', index);
        });

        slideshow.on('cycle-before', function (event, optionHash, outgoingSlideEl, incomingSlideEl, forwardFlag) {
            var index = $altima_jq('#lookbookslider_' + slider_id).data('cycle.API').getSlideIndex(incomingSlideEl);
            $altima_jq('#pagernav_' + slider_id + ' ul').cycle('goto', index);
        });
    }
});
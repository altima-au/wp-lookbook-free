var $altima_jq = jQuery.noConflict();

$altima_jq(document).ready(function() {


    $altima_jq(".shotcode_dialog" ).dialog({
        modal: true,
        autoOpen: false,
        dialogClass: "alert",
        buttons: {
            Ok: function() {
                $altima_jq( this ).dialog( "close" );
            }
        }
    });

    $altima_jq(document).on( 'click', '#wp-lb-free-notice .notice-dismiss', function() {
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'wplookbook_free_dismiss_acf_notice'
            }
        });
    });
});

function show_popup(slider_id) {
    $altima_jq( "#dialog-message_" + slider_id ).dialog( "open" );
}

function confirmDelete() {
    if (confirm("Delete file?")) {
        return true;
    } else {
        return false;
    }
}
<?php
/**
 * @param string $select_name - name for tag <select>
 * @param mixed $value - current value
 * @param array $options_list - for builds options
 * @param array $attrbutes - html attributes for tag <select>
 * @return string
 */
function alfw_form_select($select_name, $value, $options_list=array(), $attrbutes = array()) {
    $select_attr = '';
    if (count($attrbutes)) {
        foreach ($attrbutes as $name=>$val) {
            $select_attr .= $name . '="' . $val .'" ';
        }
    }

    $out = '<select name="' . $select_name . '" ' . $select_attr . '>';

    @$ar_data = (unserialize($value));

    foreach ($options_list as $opt_val=>$title){
        if ($ar_data !== false){
            $select_flag = (in_array($opt_val, $ar_data)) ? 'selected="selected"' : '';
        }else {
            $select_flag = ($opt_val == $value) ? 'selected' : '';
        }
        $out .= '<option '.$select_flag.' value="'.$opt_val.'">' . $title . '</option>'."\n";
    }
    $out .= '</select>';
    return $out;
}

/**
 * @param $file
 * @param $restrictions
 * @return array
 */
function alfw_check_slider_image_restriction($file, $restrictions) {

    if (empty($file['tmp_name'])) return array('error'=>false, 'error_msg'=>'');

    $error = false;
    $error_msg = array();

    if ($file['size'] > $restrictions['size']){
        $error = true;
        $error_msg[] = __('File size too large');
    }

    preg_match("#.+\/(.+)#siu", $file['type'], $matches);

    $allowed_ext = explode(",", $restrictions['ext']);

    array_push($allowed_ext, 'jpeg'); // :)

    if (!in_array($matches[1], $allowed_ext)){
        $error = true;
        $error_msg[] = __('This file type not allowed');
    }

    return array('error'=>$error, 'error_msg'=>$error_msg);
}

function alfw_cleen_fx(&$el_ar, $removed = array('all','none')) {
    if(in_array($el_ar, $removed)){
        unset($el_ar);
    }
}

function alfw_woocommerce_not_install_notice() {
    echo
        '<div class="error notice">
            <p>' . __('For correct work LookBookFree plugin required WOOCOMMERCE!') . '</p>
    </div>';
}
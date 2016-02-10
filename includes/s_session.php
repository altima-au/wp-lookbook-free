<?php
class s_session {

    public $s_name = "";
    public $s_id = "";
    public $s_var_prefix = "SKY";
    public $s_header_postfix = "";
    public $s_header_postfix_rewrite = "";

    function __construct ($session_name = "") {
            
        if ( $this->is_session_started() === FALSE ) {
            if (!empty($session_name)) {
                session_name($this->s_name);
            }
            session_start();
            $this->s_name = session_name();            
        }            
        
        $this->s_id = session_id();
        $this->s_header_postfix = $this->s_name."=".$this->s_id;
        $this->s_header_postfix_rewrite = $this->s_name."/".$this->s_id."/";
    }

    function s_set ($name, $var) {
        $new_name = $this->s_var_prefix.$name;
        if (is_array($var)) {
            $_SESSION[$new_name] = serialize($var);
        }else {
            $_SESSION[$new_name] = $var;
        }
    }

    function s_get ($name) {
        $new_name = $this->s_var_prefix.$name;        
        $_tmp = NULL;
        if (isset($_SESSION[$new_name])) {
            
            if ($this->isSerialized($_SESSION[$new_name])) {
                $_tmp = unserialize($_SESSION[$new_name]);
            }else{
                $_tmp = $_SESSION[$new_name];
            }
        }
        return (is_array($_tmp)) ? $_tmp : @$_SESSION[$new_name];
    }

    function s_del ($name) {
        $new_name = $this->s_var_prefix.$name;
        unset($_SESSION[$new_name]);
        if (ini_get('register_globals'))
        session_unregister($new_name);
    }

    function s_destroy () {
        foreach ($_SESSION as $var_name => $value) {
            unset ($_SESSION[$var_name]);
        }
        return session_destroy();
    }
    
    function dump($sess = array()) {
        if (empty($sess))
            $sess = $_SESSION;     
        foreach ($sess as $key => $value) {
            if (is_array($key)) {
                $this->dump($key);
            }else {
                echo ($key . " => " .  $value);
            }
        }
        
    }    
    
/**
 * @return bool
 */
function is_session_started() {
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

/**
 * $str string
 * return bool 
 */
function isSerialized($str) {
    return ($str == serialize(false) || @unserialize($str) !== false);
}
    
}

?>
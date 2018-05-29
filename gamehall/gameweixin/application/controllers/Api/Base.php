<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

abstract class Api_BaseController extends Yaf_Controller_Abstract {
    
    protected function init() {
        Yaf_Dispatcher::getInstance()->disableView();
    }
    
    public function getInput($var) {
        if (is_string($var)) return self::getVal($var);
        if (is_array($var)) {
            $return = array();
            foreach ( $var as $key => $value ) {
                $return[$value] = self::getVal($value);
            }
            return $return;
        }
        return null;
    }
    
    private function getVal($var) {
        $value = Util_Filter::post($var);
        if (! is_null($value)) return $value;
        $value = Util_Filter::get($var);
        if (! is_null($value)) return $value;
        return null;
    }
}

?>
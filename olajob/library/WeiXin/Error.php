<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class WeiXin_Error
 */
class  WeiXin_Error extends Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
}

?>
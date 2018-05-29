<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Freetribe_BaseController {

    public function indexAction() {
        echo 'Freetribe_BaseController';
        exit;
    }


    /**
     *show url
     */
    public function showurlAction() {
        echo "App_path: " . APP_PATH . "<br/>";
    }

}

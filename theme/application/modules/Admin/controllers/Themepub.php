<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class ThemepubController extends Admin_BaseController {

    public function indexAction() {
        $this->assign("meunOn", "zt_zTYunyinAdmin");
    }

}

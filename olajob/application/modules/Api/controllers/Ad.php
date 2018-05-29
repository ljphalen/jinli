<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class AdController
 * Created by sam
 */
class AdController extends Api_BaseController {

    public function listAction() {
        list(, $list) = Ola_Service_Ad::getList(0,4);
        $this->output(0,"", $list);
    }
}
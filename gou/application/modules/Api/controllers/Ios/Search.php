<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 *
 */
class Ios_SearchController extends Api_BaseController {
    /**
     * 关键字
     */
    public function keywordsAction() {
        $version = $this->getInput('data_version');
        $server_version = Gou_Service_Config::getValue('Keywords_Version');
        
        if ($version >= $server_version) $this->emptyOutput(0, '');
        
        list(,$list) =  Client_Service_Keywords::getList(1, 10, array('status'=>1));
        $data = array();
        foreach ($list as $key=>$value) {
            $data[] = $value['keyword'];
        }
        $this->output(0, '',  array('keywords'=>$data, 'version'=>$server_version));
    }
}

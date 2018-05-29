<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 
 *
 */
class HashController extends Api_BaseController {
	
    public $versions = array(1=>'H5版', 2=>'预装版', 3=>'渠道版',4=>'市场版', 5=>'APP版');

	public function indexAction() {
	    header("Content-type:text/json");
		$t = $this->getInput('t');
		$t_arr = explode(',', $t);
		
		list(, $hashs) = Stat_Service_ShortUrl::getsBy(array('hash'=>array('IN', $t_arr)), array('id'=>'DESC'));
		
		list(, $modules) = Gou_Service_ChannelModule::getAll();
		$modules = Common::resetKey($modules,'id');
		
		list(, $channels) = Gou_Service_ChannelName::getAll();
		$channels = Common::resetKey($channels,'id');
		
		$data = array();
		if($hashs) {
		    foreach ($hashs as $key=>$value) {
		        $data[] = array(
		                't'    => $t,
		                'id'   => $value['id'],
		                'version' => $this->versions[$value['version_id']],
		                'channel' => $channels[$value['channel_id']]['name'],
		                'module' => $modules[$value['module_id']]['name'],
		                'name' => $value['name'],
		                'url'  => html_entity_decode($value['url'])
		        );
		    }
		}		
		
		echo json_encode($data);
		exit;
	}
}

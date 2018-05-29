<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 * 点赞 接口
 *
 */
class PraiseController extends Api_BaseController {
	
	public function indexAction() {
		$info = $this->getInput(array('module', 'item_id'));
		$key_name = $info['module'].'_'.$info['item_id'];
		
		$cookie = json_decode(Util_Cookie::get('GOU-PRAISE', true), true);
		if(!$cookie) $cookie = array();
		
		if(in_array($key_name, $cookie)) $this->output(-1, '您已点过赞了!');
		
		array_push($cookie, $key_name);
		
		Util_Cookie::set('GOU-PRAISE', json_encode($cookie), true, strtotime("+360 day"), '/', $this->getDomain());
		
		switch ($info['module']) {
		    case 'amigo_goods':
		        $ret = Gou_Service_LocalGoods::parise($info['item_id']);
		        $item = Gou_Service_LocalGoods::getLocalGoods($info['item_id']);
		        break;
		    case 'zdm':
		        $ret = Fanfan_Service_Topic::parise($info['item_id']);
		        $item = Fanfan_Service_Topic::getTopic($info['item_id']);
		        break;
		    case 'topic':
		         $ret = Gou_Service_Topic::parise($info['item_id']);
		         $item = Gou_Service_Topic::getTopic($info['item_id']);
		        break;
		    default:
		        $ret = 0;
		}
		if(!$ret) $this->output(-1, '点赞失败,请稍后再试!');
       $this->output(0, 'success', array('praise'=>Common::parise(intval($item['praise']))));       
	}
}

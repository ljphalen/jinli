<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 *
 */
class Market_FanliController extends Api_BaseController {
	
	public $actions =array(
				'tjUrl'=>'/index/tj'
			);
	
	public $cacheKey = 'Front_Api_index';
	
	/**
	 * 返利首页icon及商品分类
	 * 
	 */
	public function indexAction() {
		//icons
		list(, $icon) = Gou_Service_Channel::getCanUseChanels(0, 4, array('type_id'=>6, 'channel_id'=>4));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$icon_data = $imgs = array();
		foreach ($icon as $key=>$value) {
			$imgs[] = $icon_data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$icon_data[$key]['link'] = $value['link'];
			$icon_data[$key]['name'] = html_entity_decode($value['name']);
		}
		$this->cache($imgs, 'fanli_icon');
		
		//type
		list(, $ptype) = Fanli_Service_Ptype::getAllType();
		//$ptype = Common::resetKey($ptype, 'id');
		
		$type = array();
		foreach ($ptype as $key=>$value) {
			list(, $type[$key]) = Fanli_Service_Type::getList(0, 8, array('status'=>1, 'type_id'=>$value['id']));
		}
		$type = self::_cookType($type);
		
		$type_data = array();
		foreach ($ptype as $key=>$value) {
			$type_data[$key]['ptype'] = $value['name'];
			$type_data[$key]['type'] = $type[$key];
		}
		$this->output(0, '', $type_data);
	}
	
	/**
	 * 查询商品
	 *
	 */
	public function searchAction() {
		$page = intval($this->getInput('page'));
		$perpage = intval($this->getInput('perpage'));
		$token = $this->getInput('token');
		$id = intval($this->getInput('id'));
		
		$commission_rate = Gou_Service_Config::getValue('fanli_commission_rate');
		if(!$commission_rate) $commission_rate = '20';
		
		if($id) {
			//点击量
			Fanli_Service_Type::updateTypeTJ($id);
		}
		
		$outer_code = '';
		$is_login = false;
		if($token) {
			$user = Fanli_Service_User::getUserBy(array('token'=>$token));
			if($user && $user['token_expire_time'] >= Common::getTime()) {
				$user_id = $user['id'];
				$outer_code = sprintf("%sX%s", 'Market', $user_id);
				Fanli_Service_User::updateUserExpiretime($user_id);
				$is_login = true;
			}
		}
		
		if ($page < 1) $page = 1;
		if(!$perpage) $perpage = 20;
	
		$keyword = $this->getInput("keyword");
		$keyword = urldecode($keyword);
	
		if(!$keyword) $keyword = '女装';
	
		$topApi  = new Api_Top_Service();
		$ret = $topApi->taobaoTbkItemsGet(array('page_no'=>$page, 'page_size'=>$perpage, 'keyword'=>$keyword, 'is_mobile'=>"true", 'outer_code'=>$outer_code, 'sort'=>"commissionNum_desc"));
		$goods = $ret['tbk_items']['tbk_item'];
		$total = $ret['total_results'];
		
		$data = array();
		if($goods){
			$goods = Common::resetKey($goods, 'num_iid');
			
			$iids = array_keys($goods);
			$rebate = $topApi->getTaobaoTaobaokeRebateAuth(array('params'=>implode(',',$iids), 'type'=>3));
			$webroot = Common::getWebRoot();
			
			foreach ($rebate as $key=>$value) {
				if($value['rebate'] == 'true') {
					$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>$value['param'], 'outer_code'=>$outer_code));
					$data[$key]['title'] = Util_String::substr(preg_replace('/<\/?span([^>]+)?>|\s+/',"",$goods[$value['param']]['title']), 0, 22, '',true);
					$data[$key]['img'] = $goods[$value['param']]['pic_url'].'_180x180.jpg';
					$data[$key]['link'] = $infos['click_url'];
					$data[$key]['price'] = $goods[$value['param']]['price'];
					//$data[$key]['promotion_price'] = $value['promotion_price'];
					//$data[$key]['commission'] = number_format($value['commission'] * ($commission_rate / 100), 2);
				}
			}
		}
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page, 'is_login'=>$is_login));
	}
	
	
	/**
	 * get taobaoke url
	 * @see Api_BaseController::getTaobaokeUrl()
	 */
	public function getTaobaokeUrlAction() {
		$iid = $this->getInput('iid');
		$token = $this->getInput('token');
		$is_login = false;
		if($token) {
			$user = Fanli_Service_User::getUserBy(array('token'=>$token));
			if($user && $user['token_expire_time'] >= Common::getTime()) {
				$user_id = $user['id'];
				$outer_code = sprintf("%sX%s", 'Market', $user_id);
				Fanli_Service_User::updateUserExpiretime($user_id);
				$is_login = true;
			}
		}
		
		//$iid = '35241454450';
		if($iid) {
			$topApi  = new Api_Top_Service();
			$taobaoke = $topApi->tbkMobileItemsConvert(array('num_iids'=>$iid, 'outer_code'=>$outer_code));
			if($taobaoke) {
				$this->output(0, '', array('taobaokeUrl'=>$taobaoke['click_url'],'is_login'=>$is_login));
			}
		}
	}
	
	/**
	 * update
	 * @see Api_BaseController::updateAction()
	 */
	public function updateAction() {
		$version_code = $this->getInput('version_code');
		$server_version = '10201010';
		$msg = '尊敬的用户，由于返利规则调整，需要您升级新到新的版本，购物才能拿到返利';
		if($version_code && $version_code < $server_version && msg) {
			$this->output(0, '', array('update_msg'=>$msg,'new_version'=>$server_version, 'iid_match'=>"[\\w\\:\\/\\.]+\\/i(\\d+)\\.htm.*"));
		} else {
			$this->output(0, '', array('update_msg'=>'','new_version'=>'', 'iid_match'=>"[\\w\\:\\/\\.]+\\/i(\\d+)\\.htm.*"));
		}
	}
	
	
	/**
	 *
	 * @param array $data
	 * return array 
	 */
	private function _cookType($data) {
		$type_list = array();
		$webroot = Common::getWebRoot();
		foreach ($data as $key=>$value) {
			foreach ($value as $k=>$v) {
				$type_list[$key][$k]['id'] = $v['id'];
				$type_list[$key][$k]['name'] = $v['name'];
				$type_list[$key][$k]['img'] = Common::getAttachPath() .$v['img'];
			}
		}
		return $type_list;
	}
}

<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Market_OrderController extends Api_BaseController {
	
	public $errors = array(
			'ERROR_LOGIN_TIMEOUT'=> array(501, '会话超时，请重新登录'),
			'ERROR_ILLEGAL_REQUEST'=> array(502, '非法的请求'),
	);
	
	/**
	 * user_info
	 */
	public function taobaoOrderAction() {
		$imei = $this->getPost('imei');
		$token = $this->getPost('token');
		$page = $this->getPost('token');
		$perpage = $this->getPost('perpage');
		
		if(!$imei) $imei = Common::getConfig('siteConfig', 'default_imei');
		
		if ($page < 1) $page = 1;
		if(!$perpage) $perpage = 10;
		if(!$token) $this->showError('ERROR_ILLEGAL_REQUEST');
		$user = Fanli_Service_User::getUserBy(array('token'=>$token));
		if(!$user || $user['token_expire_time'] < Common::getTime()) $this->showError('ERROR_LOGIN_TIMEOUT');
		if($user['last_login_imei'] != $imei) $this->showError('ERROR_ILLEGAL_REQUEST');
		
		list($total, $list) = Fanli_Service_Order::getList($page, $perpage, array('user_id'=>$user['id']));
		$topApi  = new Api_Top_Service();
		
		$data = array();
		foreach ($list as $key=>$val){
			$taoke_info = $topApi->getTbkItemInfo(array('num_iids'=>$val['num_iid']));
			$convert = $topApi->tbkMobileItemsConvert(array('num_iids'=>$val['num_iid']));
			$data[$key]['title'] = $val['item_title'];
			$data[$key]['link'] = $convert['click_url'];;
			$data[$key]['price'] = $val['num_iid'];
			$data[$key]['img'] = $taoke_info['pic_url'].'_310x310.'.end(explode(".",  $taoke_info['pic_url']));
			$data[$key]['money'] = $val['fanli'];
			$data[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
		}
		
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->clientOutput(0, 'success', array('data'=>$data));
	}
}

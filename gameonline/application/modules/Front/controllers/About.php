<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class AboutController extends Front_BaseController{
	
	/**
	 *初始化
	 */
	public function _init(){
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$path = $controller.'/'. $action;
		switch($path){
			case "About/index" :
				$right_nav = 1;
				break;
			case "About/contact" :
				$right_nav = 2;
				break;
			case "About/disclaimer" :
				$right_nav = 3;
				break;
			case "About/fcm" :
				$right_nav =4;
				break;
			case "About/apply" :
				$right_nav =5;
				break;
			case "About/questions" :
				$right_nav =6;
				break;
			case "About/feedback" :
				$right_nav =7;
				break;
			default:
				$right_nav = 1;
		}
		$this->assign('right_nav', $right_nav);
	}
	
	/**
	 * 关于我们-商务合作
	 */
	public function indexAction(){
		$this->_init();
	}
	
	/**
	 * 关于我们-联系我们
	 */
	public function contactAction(){
		$this->_init();
		$ami_sina_weibo = Game_Service_Config::getValue('ami_sina_weibo');
		$this->assign('ami_sina_weibo', $ami_sina_weibo);
	}
	
	/**
	 * 关于我们-免责声明
	 */
	public function disclaimerAction(){
		$this->_init();
		
	}
	
	/**
	 * 监护工程-防沉迷
	 */
	public function fcmAction(){
		$this->_init();
	}
	
	/**
	 * 监护工程-申请
	 */
	public function applyAction(){
		$this->_init();
	}
	
	/**
	 * 监护工程-其他问题
	 */
	public function questionsAction(){
		$this->_init();
	}
	
	
	/**
	 * 用户反馈
	 */
	public function feedbackAction(){
		$this->_init();
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function feed_postAction() {
		$info = $this->getPost(array('email','mobile', 'qq','react', 'reply','status','create_time','result'));
		$info = $this->_cookData($info);
		$result = Game_Service_React::addReact($info);
		if (!$result) $this->output(-1, '操作失败');
		$webroot = Common::getWebRoot();
		Common::alertMsg('意见反馈成功',$webroot.'/Front/Index/index/');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		$webroot = Common::getWebRoot();
		if(trim($info['react']) == '请填写反馈内容' || $info['react'] == ''){
			Common::alertMsg('反馈意见不能为空');
		}
		if(Util_String::strlen($info['react']) > 1000){
			Common::alertMsg('最多只能输入1000个字');
		}
		if(preg_match('/select|insert|update|delete|SELECT|INSERT|UPDATE|DELETE|UNION|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/', $info['react']) ){
			Common::alertMsg('反馈意见有不合法输入');
		}
		
		if((trim($info['qq']) == '' && trim($info['mobile']) == '' && trim($info['eamil']) == '') || (trim($info['qq']) == '请输入QQ号码' && trim($info['mobile']) == '请输入手机号码' && trim($info['eamil']) == '请输入邮箱')){
			Common::alertMsg('至少要填写一种联系方式');
		}
		
		if($info['qq'] != '请输入QQ号码' ){
			if($info['qq'] && !preg_match("/^[1-9]\d{5,11}$/",$info['qq']) ) {	
				Common::alertMsg('请确保你输入的QQ号码正确');
			}
		}else{
			$info['qq'] = '';
		}
		if($info['mobile'] != '请输入手机号码' ){
			if( $info['mobile'] && !preg_match('/^1[3458]\d{9}$/', $info['mobile']) ){
				Common::alertMsg('请确保你输入的手机号码正确');
			}
		}else{
			$info['mobile'] = '';
		}	
		
		if($info['email'] != '请输入邮箱' ){
			if($info['email'] && !preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/', $info['email']) ){
				Common::alertMsg('请确保你输入的邮箱地址正确');
			}
		}else{
			$info['email'] = '' ;
		}
		return $info;
	}
}
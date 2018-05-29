<?php
class CommonAction extends Action
{
	public $uid = false;
	public $email = false;
	protected $isLogin = false;
	protected $announcement = array ();
	protected $uc;
	
	protected function _initialize()
	{		
		if(C('OFFLINE') && false === strpos(C('OFFLINEALLOWIP'), get_client_ip()))
		{
			C('SHOW_PAGE_TRACE', false);
			$this->display(realpath(APP_PATH)."/Tpl/Common/offline.html");
			exit;
		}
		
		if(method_exists($this, '_before'))
			$this->_before();
		
		loadClient ( array ("Announcement") );
		$this->announcement = AnnouncementClient::getAnnouncement();
		
		$this->assign ( 'announcement', $this->announcement );
	}
	
	function _empty()
	{
		try {
			$this->display();
		} catch (Exception $e) {
			if(APP_DEBUG)
			{
				throw_exception('[空操作]'.$e->getMessage());
			}else{
				halt('非法操作了噻');
			}
		}
	}

	function user()
	{
		return $this->isLogin ? $this->user : false;
	}
	
	function referer()
	{
		//每次以跳转请求优先
		$referer = $this->_request('referer', 'urldecode', NULL);
		$referer = $this->parse_url($referer);

		//如果指定了合法的referer，则在登录和注册成功后跳转
		if(!empty($referer)){
			session('referer', $referer);
			return $referer;
		}

		$referer = session('referer') ? session('referer') : !empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"/";
		$referer = $this->parse_url($referer);

		if(strlen(__ROOT__) && (empty($referer) || strpos($referer, __ROOT__)) )
			$referer = __ROOT__;
		
		session('referer', $referer);
		return $referer;
	}
	
	/**
	 +----------------------------------------------------------
	 * 来源网址合法性检查，加入Xss检测
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 * @param string $url 需要检测的网址
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	protected function parse_url($url)
	{
		$url = dhtmlspecialchars($url, ENT_QUOTES);
		$info = parse_url($url);
		if(empty($info['scheme']) || empty($info['host']))
			return false;
		
		return $url;
	}
	
	/**
	 +----------------------------------------------------------
	 * 操作成功跳转的快捷方法
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 * @param string $message 提示信息
	 * @param string $jumpUrl 页面跳转地址
	 * @param Boolean $ajax 是否为Ajax方式
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	protected function success($message,$jumpUrl='',$ajax=false) {
		$_REQUEST['callbackType'] = isset($_REQUEST['callbackType']) ? $_REQUEST['callbackType'] : '';
		if($jumpUrl == 'closeCurrent')
		{
			$_REQUEST['callbackType'] = 'closeCurrent';
			$jumpUrl='';
		}

		if($jumpUrl == 'closeWin')
			$this->assign('closeWin', true);
		parent::success($message,$jumpUrl,$ajax);
		exit;
	}
	
	/**
	 +----------------------------------------------------------
	 * 操作错误跳转的快捷方法
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 * @param string $message 错误信息
	 * @param string $jumpUrl 页面跳转地址
	 * @param Boolean $ajax 是否为Ajax方式
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	protected function error($message,$jumpUrl='',$ajax=false) {
		parent::error($message,$jumpUrl,$ajax=false);
		exit;
	}
	
	/**
	 * 全局简单分页类
	 * @param mixed $map
	 */
	protected function _list($map=array(), $scope="default")
	{
		import("ORG.Util.PageNew");
		$post = D($this->model);
	
		$pageNum = intval($_REQUEST ['pageNum']) ? intval($_REQUEST ['pageNum']) : 1;
		$numPerPage = intval($_REQUEST ['numPerPage']) ? intval($_REQUEST ['numPerPage']) : C('PAGE_LISTROWS');
		$count = $post->where($map)->count();
	
		$log = sprintf("PAGE total %d, sql:%s", $count, $post->getLastSql());
		APP_DEBUG && LOG::write($log, LOG::DEBUG);
		
		$Page = new Page($count, $numPerPage);
		$Page->setConfig("prev","«");
		$Page->setConfig("next","»");
		$Page->setConfig("theme", "%upPage% %linkPage% %downPage%");
		$this->list = $post->where($map)->scope($scope)->page($pageNum, $numPerPage)->select();
		$this->nav_page = $Page->show();
		
		$log = sprintf("PAGE list sql:%s", $post->getLastSql());
		APP_DEBUG && LOG::write($log, LOG::DEBUG);
		
		return $this->list;
	}
	
	function tagSearch(){
		$tag = A("Home://Tag");
		$tag->search();
	}
	
	/**
	 * 过滤不合法的POST字段
	 * 需要定义$safeFields
	 */
	protected function safeField()
	{
		if(!IS_POST)
			return array();
		
		if (!isset($this->safeField))
			return $this->_post();

		foreach ($this->safeField as $k=>$v)
			$data[$v]= $this->_post($v);
		
		return $data;
	}
	
	function IS_MOBILE()
	{
		//判断是否属手机
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		$mobile_agents = array("acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
		$is_mobile = false;
		foreach ($mobile_agents as $device) {
			if (stristr($user_agent, $device)) {
				$is_mobile = true;
				break;
			}
		}
		
		//包含分辨率的
		if(preg_match("@\d{3,4}x\d{3,4}@is", $user_agent))
			return true;
		
		return $is_mobile;
	}

}

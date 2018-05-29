<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class FeedController extends Front_BaseController {

    public $actions = array(
        'listUrl' => '/feed/index',
    	'feedPostUrl' => '/feed/feed_post',
    	'secoderUrl' => '/feed/secoder'
    );
    
    public $perpage = 5;
	
    /**
     * 
     */
    public function indexAction() {
    	
    	list($total, $reacts) = Gou_Service_React::getList(1, $this->perpage, array('status'=>2));
    	$this->assign('react_total', $total);
    	$this->assign('reacts', $reacts);
    }
    
    /**
     * 
     */
    public function listAction() {
    	$page = intval($this->getInput('page'));
    	if ($page < 1) $page = 1;
    	
    	list($total, $reacts) = Gou_Service_React::getList($page, $this->perpage, array('status'=>2));
    	foreach ($reacts as $key=>$value) {
    		$reacts[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
    		$reacts[$key]['mobile'] = substr_replace($value['mobile'], '******', 3, 6);
    	}
    	
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	$this->output(0, '', array('list'=>$reacts, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
    
    /**
     * 
     */
    public function feed_postAction() {
    	$info = $this->getInput(array('mobile', 'code', 'react'));
    	
    	$data = self::_cookData($info);
    	if (Common::isError($data)) {
    		$this->output($data['code'], $data['msg']);
    	}
    	
    	$scode = $_SESSION['verify_py']['code'];
    	if ($scode != strtoupper($data['code'])) {
    		$this->output(-1, '验证码输错咯，重试下吧，点击图片刷新.');
    	}
    	
    	$ret = Gou_Service_React::addReact($data);
    	if (!$ret) {
    		$this->output(-1, '提交失败.');
    	}
    	$this->output(0, '提交成功.');
    }
    
    /**
     * 
     */
    public function secoderAction() {
    	$coder = new Util_Secoder();
    	$coder->useNoise = false;  //是否启用噪点
    	$coder->useCurve = false;   //是否启用干扰曲线
    	$coder->entry();
    	exit;
    }
    
    
    private static function _cookData($data) {
    	if (!$data['mobile']) return Common::formatMsg(-1, "手机号码不能为空.");
    	if (!preg_match('/^1[3458]\d{9}$/', $data['mobile'])) return Common::formatMsg(-1, "手机号码不对哦，再输一遍吧！");
    	if (!$data['react']) return Common::formatMsg(-1, '发话内容不能为空.');
    	if (!$data['code']) return Common::formatMsg(-1, '验证码不能为空.');
    	if (Util_String::strlen($data['react'], utf8) >124) return Common::formatMsg(-1, '输入内容过长.');
    	return $data;
    }
}
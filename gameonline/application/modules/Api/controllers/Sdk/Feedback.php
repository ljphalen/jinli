<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Sdk_FeedbackController extends Api_BaseController {
	public $perpage = 10;
	
	/**
	 * 提问题
	 */
	public function addFeedbackAction(){
	
	    $sp = $this->getInput('sp');
		$uname = $this->getInput('account');
		$uuid = $this->getInput('uuid');
		$phone  = $this->getInput('phone');
		//问题qq 附件的id
		$qq  =   $this->getInput('qq');
		$tag =   $this->getInput('tag');
		$content= trim($this->getInput('content'));
		
		//提问题的游戏包名
		$game_package = $this->getInput('gamePackage');
		$game_params['package'] = $game_package;
		$game_info = Resource_Service_Games::getBy($game_params);
		
		$data['sign'] ='GameSDK';
		if(!$game_info['id']){
			$this->clientOutput(array());
		}
		if(!$content){
			$this->clientOutput(array());
		} 
		
		$arr_sp = explode("_", $sp);
		//imei号
		$imei = $arr_sp[6];
		//sdk的包名
		if($imei) $imeicrc = crc32($imei);
		//获取机型
		$mode = $arr_sp[0];
		//获取sdk版本
		$version = $arr_sp[1];
		//获取android版本
		$sys_version = substr($arr_sp[3],7);
		

        //帐号
		if($uname) {
			$userInfo = Account_Service_User::getUserInfo(array('uname'=>$uname));
			$nickname = $userInfo['nickname'];
		} else {
			$nickname = $mode.'用户';
		}
	/* 	//用户不存在
		if(!$userInfo){
			$this->clientOutput(array());
		} */
		
		//提交信息
		$info = array(
				'content'=>$content,
				'uuid'=>$uuid,
				'uname'=> $uname,
				'nickname'=>$nickname,
				'imei'=> $imei,
				'imcrc'=> $imeicrc,
				'game_id'=>$game_info['id'],
				'create_time'=>Common::getTime(),
				'model'=>$mode,
				'version'=>$version,
				'sys_version'=>$sys_version,
				'tel'=>$phone,
				'status'=>0,
				'client_pkg'=>3,
				'qq' =>$qq,
				'tag' =>$tag,
		);
		
		$rs = Sdk_Service_Feedback::getBy(array('tag'=>$tag));
		if($rs){
			$ret = Sdk_Service_Feedback::updateBy($info, array('tag'=>$tag));
		}else{
			$ret  = Sdk_Service_Feedback::add($info);
		}
		if(!ret)  $this->clientOutput(array());
		$data['success'] =true;
		$this->clientOutput($data);
	}
	
	/**
	 * 我的问题
	 */
	public function myFeedbackAction(){
		
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$data['sign'] ='GameSDK';
		$uname = $this->getInput('account');
		$uuid = $this->getInput('uuid');
		if(!$uname || !$uuid){
			$this->clientOutput(array());
		}
		
		$params['uuid']  = $uuid ;
		//$params['uname']  = $uname ;
        //取得结果
		list($total,$result) = Sdk_Service_Feedback::getList($page, $this->perpage, $params, array('create_time'=>'DESC','id'=>'DESC'));
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$data['myQuestions'] = array();
		foreach ($result as $val){
			$data['myQuestions'][] = array('question' => html_entity_decode($val['content'], ENT_QUOTES),
										   'answer'  => html_entity_decode($val['reply_content'], ENT_QUOTES),
										   'time' => date('Y-m-d', $val['create_time']),
					);
		}
		$data['hasNext'] = $hasnext ;
		$data['curPage'] = intval($page) ;
		$data['totalCount'] = intval($total) ;
	    $this->clientOutput($data);
		
	}
	
	/**
	 * 附件
	 */
	public function attachAction(){
		//header("Content-type:text/html;charset=utf-8");
		
		$data['sign'] ='GameSDK';
	 	$tag = $this->getInput('tag');
		$account = trim($this->getInput('account'));
		$uuid = trim($this->getInput('uuid'));
		

		//验证合法性
        if(!$tag || !$account){
        	$data['success'] = false;
        	$data['msg']     = '非法参数'.$account.$tag;
        	$this->clientOutput($data);
        }
		
		if(!$_FILES['img']){
			$data['success'] = false;
			$data['msg']     = '没有上传文件';
			$this->clientOutput($data);
		} 
		
		
	/* 	$ret = Sdk_Service_Feedback::getBy(array('tag'=>$tag,'uname'=>$account));
		if(!$ret){
			$data['success'] = false;
			$data['msg']     = '非法用户';
			$this->clientOutput($data);
		} */

	 
        //图片的格式
		$ext  = strtolower(substr(strrchr($_FILES['img']['name'], '.'), 1));
        if( !in_array($ext, array('jpg' ,'jpeg','png' ,'gif')) ){
        	$data['success'] = false;
			$data['msg']     = '格式不对';
			$this->clientOutput($data);
        }
        
        //大小的验证
       if($_FILES['img']['size'] < 1 || $_FILES['img']['size'] > (2048 * 1024)){
        	$data['success'] = false;
			$data['msg']     = '上传文件最大为2M'.$_FILES['img']['size'].$_FILES['img']['name'];
			$this->clientOutput($data);
        }
        
        $attachPath = Common::getConfig('siteConfig', 'attachPath');//接收文件目录
        $savePath = sprintf('%s%s/%s/', $attachPath,'sdk',date('Ym'));
        $this->mkRecur($savePath);

        //文件名
        $filename = time();
        $target_path = $savePath . $filename.'.'.$ext;
        $new_filename = 'sdk/'.date('Ym').'/'.$filename.'.'.$ext;
		move_uploaded_file( $_FILES['img']['tmp_name'], $target_path);  
		
		//$attachPath = Common::getConfig('siteConfig', 'attachPath');//接收文件目录
		//$target_path = '/home/ljp/www/motion/trunk/game/public/admin/../../../attachs/game/attachs/sdk/201409/542287e9b8ed9.png';
		//$temp_path ='sdk/201409/542287e9b8ed9.png';
		
		//获取配置信息
		$config = Common::getConfig('ftpConfig');
		// 打开FTP连接
		$ftp = new Util_Ftp($config['host'], $config['port'], $config['user'], $config['pass']); 
		//上传文件
	    $rs = $ftp->up_file($target_path,$new_filename);
	  /*   if(!$rs){
	     	$data['success'] = false;
	     	$data['msg']     = '上传失败';
	     	$this->clientOutput($data);
	    }  */
	    $ftp->close();                                            

		$info['attach'] = $new_filename;
		$params['tag']    = $tag ;
		$params['uname']  = $account ;
		$params['uuid']  =  $uuid ;
		$ret = Sdk_Service_Feedback::getBy(array('tag'=>$tag));
		if($ret){
			$result = Sdk_Service_Feedback::updateBy($info, $params);
		}else{
			$info['tag'] = $tag;
			$info['uname'] = $account;
			$info['uuid'] =  $uuid;
			$result = Sdk_Service_Feedback::add($info);
		}
		
		if(!$result){
			$data['success'] = false;
			$data['msg']     = '数据库繁忙';
			$this->clientOutput($data);
		}
		$data['success'] =true;
		$this->clientOutput($data); 
	}
	
	/**
	 * 小红点
	 */
	public function hotAction(){

	}
	
	private  function mkRecur($path, $permissions = 0777) {
		if (is_dir($path)) return true;
		$_path = dirname($path);
		if ($_path !== $path) self::mk($_path, $permissions);
		return self::mk($path, $permissions);
	}
	
	private  function mk($path, $permissions = 0777) {
		return @mkdir($path, $permissions);
	}


}

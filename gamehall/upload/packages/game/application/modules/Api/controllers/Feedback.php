<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class FeedbackController extends Api_BaseController {
	public $perpage = 10;
	
	/**
	 * 提问题
	 */
	public function addFeedbackAction(){
	
	    $sp = $this->getInput('sp');
		$uname = trim($this->getInput('uname'));
		$uuid = trim($this->getInput('puuid'));
		$contact  = $this->getInput('contact');
		$content= trim($this->getInput('content'));
		$clientPackage = trim($this->getInput('client_pkg'));
		
		$data['success'] ='true';
		$data['msg'] ='';
		$data['sign'] ='GioneeGameHall';
		if(!$content || !$sp || !$contact || !$clientPackage){
			$this->clientOutput(array());
		} 
		
		
	    $spArr = Common::parseSp($sp);
		$imei = $spArr['imei'];
		if($imei){
			$imeicrc = crc32($imei);
		} 
		
		if($imei == 'FD34645D0CF3A18C9FC4E2C49F11C510'){
			$imei = '';
		}
		//获取机型
		$mode = $spArr['device'];
		$clientVertion = $spArr['game_ver'];
		$sysVersion = $spArr['android_ver'];
		
	    if(!$imei && (!$uname || !$uuid )){
			$this->clientOutput(array());
		}
		//提交信息
		$info = array(
				'content'=>$content,
				'uuid'=>$uuid,
				'uname'=> $uname,
				'imei'=> $imei,
				'imcrc'=> $imeicrc,
				'create_time'=>Common::getTime(),
				'model'=>$mode,
				'client_version'=>$clientVertion,
				'sys_version'=>$sysVersion,
				'contact'=>$contact,
				'status'=>0,
				'client_pkg'=>$clientPackage
		);
		
		$ret  = Feedback_Service_Feedback::add($info);
		if(!ret){
			$data['success'] ='false';
			$this->clientOutput($data);
		}
		$data['feedbackId'] =$ret;
		$this->clientOutput($data);
	}
	
	/**
	 * 我的问题
	 */
	public function myFeedbackListAction(){

		$uname = $this->getInput('uname');
		$uuid  = $this->getInput('puuid');
		$imei = $this->getInput('imei');
		$version = $this->getInput('dataVersion');
			
		if(!$imei && (!$uname || !$uuid )){
			$this->clientOutput(array());
		}  
		
		$data['success'] ='true';
		$data['msg'] ='';
		$data['sign'] ='GioneeGameHall';
		
		if($uname){
			$params['uname']  = $uname ;
		}else{
			if($imei == 'FD34645D0CF3A18C9FC4E2C49F11C510'){
				$this->clientOutput($data);
			}
			$params['imei']  = $imei ;
			$online = Account_Service_User::checkOnline($uname,  $imei);
			if(!$online){
				$params['uname'] = array('=', '');
			}
			
		}
		
		$ret = Feedback_Service_Feedback::getBy($params, 
				                                array('version_time'=>'DESC','id'=>'DESC'));
		$data['version'] = strval($ret['version_time']);
		if( isset($version) && $version >= $data['version'] ){
			$data['data'] = (object)array();
			$this->clientOutput($data);
		}
		

		
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
        //取得结果
		list($total,$result) = Feedback_Service_Feedback::getList($page, 
				                                                  $this->perpage,
				                                                  $params, 
				                                                   array('create_time'=>'DESC', 
				                                                   		 'id'=>'DESC') );
		$hasNext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$data['data']['list'] = array();
		foreach ($result as $val){
			$reply = Feedback_Service_FeedbackReply::getByID($val['id']);
			$data['data']['list'][] = array('question' => html_entity_decode($val['content'], ENT_QUOTES),
			                                'questionTime'=> strval($val['create_time']),
										    'answer'  => $reply['reply_content']?html_entity_decode($reply['reply_content'], ENT_QUOTES):'',
					                        'answerTime'  => $reply['reply_time']?strval($reply['reply_time']):''
					                      );
		}	

		$data['data']['curpage'] = intval($page) ;
		$data['data']['totalCount'] = intval($total) ;
		$data['data']['hasnext'] = $hasNext;
	    $this->clientOutput($data);
		
	}
	
	/**
	 * 附件
	 */
	public function attachAction(){
		$sp = $this->getInput('sp');
		$feedbackId = intval($this->getInput('feedbackId'));
		$data['success'] ='false';
		$data['msg'] ='';
		$data['sign'] ='GioneeGameHall';
		//验证合法性
        if(!$feedbackId){
        	$data['msg']     = '非法参数';
        	$this->clientOutput($data);
        }
        
        $spArr = Common::parseSp($sp);
        $imei = $spArr['imei'];
        $ret = Feedback_Service_Feedback::getBy(array('id'=>$feedbackId,'imei'=>$imei));
        if(!$ret){
        	$data['msg']     = '非法';
        	$this->clientOutput($data);
        }
        
		if(!$_FILES['img']){
			$data['msg']     = '没有上传文件';
			$this->clientOutput($data);
		} 
		 
        //图片的格式
		$ext  = strtolower(substr(strrchr($_FILES['img']['name'], '.'), 1));
        if( !in_array($ext, array('jpg' ,'jpeg','png' ,'gif')) ){
			$data['msg']     = '格式不对';
			$this->clientOutput($data);
        }
        
        //大小的验证
       if($_FILES['img']['size'] < 1 || $_FILES['img']['size'] > (2048 * 1024)){
			$data['msg']     = '上传文件最大为2M'.$_FILES['img']['size'].$_FILES['img']['name'];
			$this->clientOutput($data);
        }
        
        $attachPath = Common::getConfig('siteConfig', 'attachPath');//接收文件目录
        $savePath = sprintf('%s%s/%s/', $attachPath,'sdk',date('Ym'));
        $this->mkRecur($savePath);

        //文件名
        $filename = time();
        $target_path = $savePath . $filename.'.'.$ext;
        $new_filename = 'feedback/'.date('Ym').'/'.$filename.'.'.$ext;
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

		$info['image_path'] = $new_filename;
		$params['feedback_id']  = $feedbackId ;
		$ret = Feedback_Service_FeedbackAttach::getBy(array('feedback_id'=>$feedbackId));
		if($ret){
			$result = Feedback_Service_FeedbackAttach::updateBy($info, $params);
		}else{
			$info['feedback_id']  = $feedbackId ;
			$info['create_time'] = Common::getTime() ;
			$result = Feedback_Service_FeedbackAttach::add($info);
		}
		if(!$result){
			$data['msg']     = '数据库繁忙';
			$this->clientOutput($data);
		}
		$data['success'] = 'true';
		$this->clientOutput($data); 
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
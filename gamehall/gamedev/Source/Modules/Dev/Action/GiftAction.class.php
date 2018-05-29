<?php
class GiftAction extends BaseAction
{
	public function manage()
	{
		$id = $this->_get('id','intval,trim',0);
		$status = $this->_get('status','trim', null);
		
        if(empty($id) || !$app = D("Apks")->where(array("author_id"=>$this->uid, "app_id"=>$id))->find())
        	return $this->error('不存在该应用!');
        
        $where = array("app_id"=>$id);
        if(is_numeric($status) && in_array($status, array_keys(GiftModel::$status)))
        	$where["status"] = $status;
        else
        	$status = "nil";
        
        $gifts = D("Gift")->where($where)->order('id desc')->select();
        
        $this->assign('status', $status);
        $this->assign('app', $app);
        $this->assign('gifts', $gifts);
        $this->display();
	}
	
    // 添加礼包
    public function add()
    {
        $id = $this->_get('id','intval,trim',0);
        if(empty($id) || !$app = D("Apks")->find($id))
        	return $this->error('不存在该应用!');

        $this->assign('app', $app);
        $this->display('add');
    }
    
    // 修改礼包
    public function edit()
    {
    	$id = $this->_get('id','intval,trim', 0);
    	if(empty($id) || !$gift = D("Gift")->where(array("id"=>$id, "author_id"=>$this->uid))->find())
    		return $this->error('不存在该礼包!');
    	
    	$this->assign('app', D("Apks")->find($gift["apk_id"]));
    	$this->assign("gift", $gift);
    	$this->display();
    }

    // 礼包提交
    public function save()
    {
    	//是否为编辑状态
    	$edit_id = $this->_post('edit_id','intval', 0);
    	
        $name = $this->_post('name','trim','');
        $content = $this->_post('content','trim','');
        $method = $this->_post('method','trim','');
        $vtime_from = $this->_post('vtime_from','trim,strtotime','');
        $vtime_to = $this->_post('vtime_to','trim,strtotime','');
        $saveSub = $this->_post('saveSub','intval',0);
        $app_id = $this->_post('app_id','intval',0);
        $apk_id = $this->_post('apk_id','intval',0);

        if(empty($name)) $this->error('请填写礼包名称');
        if(empty($content)) $this->error('请填写礼包内容');
        if(empty($method)) $this->error('请填写礼包使用方法');
        if(empty($vtime_from)) $this->error('请填写礼包开始时间');
        if(empty($vtime_to)) $this->error('请填写礼包结束时间');
        if(empty($app_id) || empty($apk_id)) $this->error('不存在该应用');

        // 兑换有效期检测
        if($vtime_from >= $vtime_to) $this->error('您填写的兑换期不正确');

        $data = array(
            'name' => $name,
            'content' => $content,
            'method' => $method,
            'vtime_from' => $vtime_from,
            'vtime_to' => $vtime_to,
            'app_id' => $app_id,
        	'apk_id' => $apk_id,
            'ctime' => time(),
        	'author_id' => $this->uid,
            'status' => $saveSub == 1 ? 1 : 0
        );

        if($edit_id > 0 && $edit_id == D("Gift")->where(array("id"=>$edit_id, "author_id"=>$this->uid))->getField('id'))
        {
        	D("Gift")->data($data)->where(array("id"=>$edit_id))->save();
        	
        	$msg = $data["status"] > 0 ? "礼包已提交，请等待审核" : "礼包修改成功";
        	return $this->success($msg, U("Gift/manage", array("id"=>$data["app_id"])));
        }
        
        // 上传礼包文件
        $uploadSetting = array(
        		'maxSize'	=>2*1024*1024,			//文件大小限制
        		'allowExts'	=>array('txt'),	//文件类型设置
        );
        $uploadList = helper ( "Upload" )->_upload ("user", $uploadSetting);
		if(!is_array($uploadList[0]))
			return $this->error($uploadList);
		
		//读取总记录数
		$file = helper("Apk")->get_path("user").$uploadList[0]["filepath"];
		$data["total"] = count(file($file));
		$data["filename"] = $uploadList[0]["name"];
		$data["filepath"] = $uploadList[0]["filepath"];
		D("Gift")->add($data);

		$msg = $data["status"] > 0 ? "礼包已提交，请等待审核" : "礼包添加成功";
		$this->success($msg, U("Gift/manage", array("id"=>$data["app_id"])));
    }

    // 兑换时间检查
    public function validate_time_check(){
        $status = 1;
        $vtime_from = strtotime($_POST["vtime_from"]);
        $vtime_to = strtotime($_POST["vtime_to"]);

        if($vtime_from >= $vtime_to)
            $status = 0;

        $this->ajaxReturn(array('status'=>$status));
    }
}
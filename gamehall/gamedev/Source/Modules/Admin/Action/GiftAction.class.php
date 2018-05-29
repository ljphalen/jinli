<?php
class GiftAction extends SystemAction
{
    public $model = "Dev://Gift";

    public function _filter(&$map)
    {
        $_search = MAP();
        $map = !empty($_search) ? array_merge($_search, $map) : $map;
        
        if(isset($map["app_name"]))
        {
        	$app_id = D("Apps")->where(array("app_name"=>array("like", "%{$map["app_name"]}%")))->getField("id",true);
        	$map["app_id"] = array("in", $app_id);
        }
        
        if(isset($map["app_status"]))
        {
        	$app_id = D("Apks")->where(array("status"=>$map["app_status"]))->getField("id",true);
        	$map["apk_id"] = array("in", $app_id);
        }
        
        if(isset($map["timeStart"]))
        	$map["vtime_from"] = array("gt", strtotime($map["timeStart"])-1);
        if(isset($map["timeEnd"]))
        	$map["vtime_to"] = array("lt", strtotime($map["timeStart"])+1);
        
        $this->assign("_search", $_search);
    }
    
    public function _before_update()
    {
    	$_POST["admin_id"] = admin_id();
    	$_POST["atime"] = time();
    }
    
    //礼包审核通过，则将兑换码写入数据库
    public function _after_update()
    {
    	$status = $this->_post('status');
    	$id = $this->_post('id');
    	if($status > 0 && $id > 0 && $vo = D($this->model)->where(array("id"=>$id, "status"=>$status))->find())
    	{
    		//如果已经添加过
    		if(D("GiftCodes")->where(array("gift_id"=>$id))->count())
    			return;
    		
    		$file = Helper ( "Apk" )->get_path ( "user" ) . $vo["filepath"];
    		$code = file($file);
    		
    		$dataList = array();
    		foreach ($code as $c)
    		{
    			$c = trim($c);
    			if(strlen($c) < 1)
    				continue;

    			$dataList[] = array(
    					"gift_id"         => $id,
    					"app_id"          => $vo["app_id"],
    					"vtime_from"      => $vo["vtime_from"],
    					"vtime_to"        => $vo["vtime_to"],
    					"status"          => $status,
    					"code"            => $c,
    			);
    		}
    		D("GiftCodes")->addAll($dataList);
    	}

        // 发送邮件
        $vo = D($this->model)->where(array("id"=>$id))->find();
        $app_name = D('apps')->where(array('id'=>$vo['app_id']))->getField('app_name');
        $contact_email = D('accounts')->where(array('id'=>$vo['author_id']))->getField('email');
        $vo['app_name'] = $app_name;
        $vo['contact_email'] = $contact_email;

        if($status < 0){
            self::send_mail($vo,0);
        }else {
            self::send_mail($vo,1);
        }
    }
    
    public function view()
    {
    	$this->edit();
    }
    
    public function off()
    {
    	$id = $this->_get("id");
    	
    	D($this->model)->where(array("id"=>$id))->data(array("admin_id"=>admin_id(), "atime"=>time(), "status"=>4))->save();
    	$this->success("下线成功");
    }

    protected function send_mail($key,$type)
    {
        $res = $type ? '通过':'不通过';
        $app_name = $key['app_name'];
        $sendemail = $key['contact_email'];
        $name = $key['name'];
        $time = date('Y-m-d H:i:s',time());
        $subject = sprintf("【%s】《%s》礼包审核%s", C("SMTP.SMTP_NAME"), $app_name,$res);
        $body = <<<EEE
        亲爱的开发者：<br>
<p>您的应用《%s》的礼包《%s》审核%s。</p>
<p>*如有问题，还请邮件至dev.game@gionee.com，或拨打我们的开发者客服电话：0755-83211672<br>
金立游戏开发者平台</p>
<p>%s</p>
EEE;
        $body = sprintf($body, $app_name, $name,$res,$time);
        smtp_mail($sendemail, $subject, $body);
    }
}
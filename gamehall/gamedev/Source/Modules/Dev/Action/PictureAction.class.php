<?php
//删除应用编辑时，上传的截图、icon、版权证书
class PictureAction extends BaseAction {
	function del(){
		$id = $this->_get("id", "intval",0);

		//验证删除权限
		$aid = D("Picture")->where(array("id"=>$id))->getField("app_id");
		$app = D("Apps")->find($aid);
		if($app["author_id"] == $this->uid)
			$res = D("Picture")->where(array("id"=>$id))->save(array('status'=>0));
		die;
	}
	
	function del_copyright(){
		$get = $this->_get ();
		
		//标记产权证状态
		$apkModel = D("Dev://Apks");
		$apkModel->data(array("app_cert"=>0))->where(array("author_id"=>$this->uid, "id"=>$get['apk_id']))->save();

		$Picture = D("AppCert");
		$data = array();
		$data ['account_id'] = $this->uid;
		$data ['app_id'] = $get ['app_id'];
		$data ['apk_id'] = $get ['apk_id'];
		$Picture->delete($data);
	}
}
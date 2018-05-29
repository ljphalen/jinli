<?php

class PictureAction extends BaseAction {
	function del(){
		$id = $this->_get("id","intval",0);
		$res = D("Picture")->where(array("id"=>$id))->save(array('status'=>0));
		die;
	}
}

?>
<?php
/**
 * 金立游戏分类数据同步
 * 根据需要执行 php cli.php Sync get
 */
class SyncAction extends CliBaseAction
{
	
	/*
	 * 数据同步
	 */
	public function get()
	{
		$this->printf("--- start ---");
		
		$Category = D("Dev://Category");
		$Feetype = D("Admin://Feetype");
		$Reso = D("Dev://Reso");
		$Label = D("Dev://Label");
		
		//游戏分类
		$url = C('API_URL_GAME_CATE');
		$res = $this->curlpost($url);
		
		if(!empty($res)){
			$data = array();
			//先清除老数据
			$Category->query('truncate category');
			foreach ($res as $key=>$value){
				$data['id'] = $value['id'];
				$data['name'] = $value['title'];
				$data['parent_id'] = 100;
				if($value['id'] != 100)
				{
					$this->printf("\t\ name:%s, id:%d", $data['name'], $data['id']);
					$Category->add($data,$options=array(),$replace=TRUE);
				}
				
				//子分类
				if(isset($value["items"]))
				{
					foreach ($value["items"] as $subCate)
					{
						$data['id'] = $subCate['id'];
						$data['name'] = $subCate['title'];
						$data['parent_id'] = $value['id'];
						$Category->add($data,$options=array(),$replace=TRUE);
					}
				}
			}

			$this->printf("------->%d category found", count($res));
		}
		
		//收费类型
		$url = C('API_URL_FEE_TYPE');
		$res = $this->curlpost($url);
		if(!empty($res)){
			$data = array();
			//先清除所有数据
			$Feetype->query('truncate feetype');
			foreach ($res as $key=>$value){
				$data['id'] = $value['id'];
				$data['type_name'] = $value['title'];
				
				$this->printf("\t\ name:%s, id:%d", $data['type_name'], $data['id']);
				$Feetype->add($data,$options=array(),$replace=TRUE);
			}
			
			$this->printf("------->%d fee type found", count($res));
		}
		
		//分辨率
		$url = C('API_URL_RESO');
		$resoRes = $this->curlpost($url);
		if(!empty($resoRes)){
			$data = array();
			//先清除所有数据
			$Reso->query("truncate reso");
			foreach ($resoRes as $key=>$value){
				$data['reso_id'] = $value['id'];
				$data['reso_name'] = $value['title'];
				
				//填写分辨率宽高字段
				list($w, $h) = explode("*", $value['title']);
				$data['width'] = $w;
				$data['height'] = $h;

				$this->printf("\t\ reso_name:%s, width:%s, height:%s", $data['reso_name'], $data['width'], $data['height']);
				$Reso->add($data,$options=array(),$replace=TRUE);
			}
			
			$this->printf("------->%d reso found", count($resoRes));
		}
		
		//标签
		$url = C('API_URL_LABEL');
		$url_label = C('API_URL_LABEL_CHILD');
		$res = $this->curlpost($url);
		if(!empty($res)){
			$data = array();
			$Label->query("truncate label");
			foreach ($res as $key=>$value){
				$data['id'] = $value['id'];
				$data['name'] = $value['title'];
				$data['parent_id'] = 0;
				$result = $Label->add($data,$options=array(),$replace=TRUE);
				if($result){
					$res_label = $this->curlpost($url_label.$value['id']);
					if(!empty($res_label)){
						$data_label = array();
						foreach ($res_label as $lkey => $lvalue){
							$data_label['id'] = $lvalue['id'];
							$data_label['name'] = $lvalue['title'];
							$data_label['parent_id'] = $value['id'];
							
							$this->printf("\t\ label_name:%s, id:%s", $data_label['name'], $data_label['id']);
							$Label->add($data_label,$options=array(),$replace=TRUE);
						}
					}
				}
			}
			$this->printf("------->%d lebel found", count($res));
		}
		
		$this->printf("--- done ---");
		exit(0);
	}
	
	/*
	 * Post请求
	 */
	function curlpost($url){
		$res = `curl -s $url`;
		$jsonArr = json_decode($res,true);
		if($jsonArr['success'] == 1)
			return $jsonArr['data'];
		else
			$this->printf("CURL FAILD : ".$url);
		
		return array();
	}
}
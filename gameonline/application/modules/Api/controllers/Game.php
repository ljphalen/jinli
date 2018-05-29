<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GameController extends Api_BaseController {
	
	
    /**
     * 
     */
    public function attributeAction() {
    	$sid = $this->getInput('sid');
    	if (!$sid) $this->output(-1, 'invalid sid.', array());
    	$params = array('at_type'=>$sid, 'status' => '1');
    	//分类属性特殊处理
    	if($sid == '1') $params['editable'] = 0;
    	list(, $result) = Resource_Service_Attribute::getList(0,500, $params);
    	
    	$tmp = array();
    	foreach($result as $key=>$value) {
    		$tmp[] = array('id'=>$value['id'], 'title'=>$value['title'], 'status'=>$value['status']);
    	}
    	$this->output(0, '', $tmp);
    }
    
    public function labelAction() {
    	$sid = $this->getInput('lid');
    	if (!$sid) $this->output(-1, 'invalid lid.', array());
    	 
    	list(, $result) = Resource_Service_Label::getList(0,500, array('btype'=>$sid,'status' => '1'));
    	 
    	$tmp = array();
    	foreach($result as $key=>$value) {
    		$tmp[] = array('id'=>$value['id'], 'title'=>$value['title'], 'status'=>$value['status']);
    	}
    	$this->output(0, '', $tmp);
    }
    
    public function testAction(){
    	$gid =  $this->getInput('id');
    	$file = $this->getInput('file');
    	if(!$gid) die('id not found');
    	header("Content-type: text/html; charset=utf-8");

		//获取所有分类-通过
		list(, $categorys) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>1,'status'=>1));
		$categorys = Common::resetKey($categorys, 'id');
		//游戏所有标签分类-通过
		list(, $lab_categorys) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>8,'status'=>1));
		$lab_categorys = Common::resetKey($lab_categorys, 'id');
		//游戏所有标签分类 值-通过
		list(, $labels) = Resource_Service_Label::getAllSortLabel();
		$lab_value = array();
		foreach($labels as $key=>$value){
			$lab_value[$value['btype']][] = $value;
		}
    	//所有系统版本属性值
    	list(, $sys_version) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>5,'status'=>1));
    	$sys_version = Common::resetKey($sys_version, 'id');
    	//所有分辨率属性值	
    	list(, $resolution) = Resource_Service_Attribute::getList(1, 500,array('at_type'=>4,'status'=>1));
    	$resolution = Common::resetKey($resolution, 'id');
      	echo "===============================游戏资料输出开始=======================================";
    	echo '<br/><br/><br/><br/><br/><br/>';
			$page = array();
			//游戏内容-通过
			$info = Resource_Service_Games::getResourceGames($gid);
			if(!$info) die('game not found');
			echo "游戏名称:&nbsp;&nbsp;&nbsp;&nbsp;" . $info['name'] . '<br/>';
			array_push($page, array("游戏名称:" ,  $info['name']));
			//游戏包名
			echo "游戏包名:&nbsp;&nbsp;&nbsp;&nbsp;" .  $info['package'] . '<br/>';
			array_push($page, array("游戏包名:" ,  $info['package']));
			//游戏分类-通过
			$category_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>$gid));
			$category_title = self::_gameData($categorys,$category_ids,'category_id');
			$category_title = implode(',', $category_title);
			echo "游戏分类:&nbsp;&nbsp;&nbsp;&nbsp;" .  $category_title . '<br/>';
			array_push($page, array("游戏分类:" ,  $category_title ));
			 
			//该游戏所有标签ID
			$game_labels = Resource_Service_Games::getIdxLabelByGameId($gid);
			$game_labels = Common::resetKey($game_labels, 'label_id');
			$game_labels = array_keys($game_labels);
			//完成游戏标签分类值组装
			$labelsArr = array();
			foreach($lab_categorys as $key=>$value){
				$str = '';
				$valueArr = array();
				foreach($lab_value[$value['id']] as $k=>$v){
					if(in_array($v['id'], $game_labels))	$valueArr[] = $v['title'];
				}
				$str .= implode(',', $valueArr) ;
				echo $value['title'].':&nbsp;&nbsp;&nbsp;&nbsp;' . $str . '<br/>';
				array_push($page, array($value['title'] , $str));
			}
			 
			//计费方式
			list(, $price) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>3,'status'=>1));
			$price = Common::resetKey($price, 'id');
			$price_title  = $price[$info['price']]['title'];
			
			echo "计费方式:&nbsp;&nbsp;&nbsp;&nbsp;" .  $price_title. '<br/>';
			array_push($page, array("计费方式:" ,  $price_title ));
			//来源
			$source = $info['company'];
			echo "来源:&nbsp;&nbsp;&nbsp;&nbsp;" .  $source . '<br/>';
			array_push($page, array("来源:" ,  $source ));
			//语言
			$language = $info['language'];
			echo "语言:&nbsp;&nbsp;&nbsp;&nbsp;" .  $language . '<br/>';
			array_push($page, array("语言:" ,  $language ));
			//简述
			$resume = $info['resume'];
			echo "简述:&nbsp;&nbsp;&nbsp;&nbsp;" .  html_entity_decode($resume) . '<br/>';
			array_push($page, array("简述:" ,  $resume ));
			//热词
			$hotkey = $info['label'];
			echo "热词:&nbsp;&nbsp;&nbsp;&nbsp;" .  $hotkey . '<br/>';
			array_push($page,  array("热词:" ,  $hotkey ));
			//应用介绍
			$descrip = $info['descrip'];
			echo "介绍:&nbsp;&nbsp;&nbsp;&nbsp;" .  html_entity_decode($descrip) . '<br/>';
			array_push($page, array("介绍:" ,  html_entity_decode($descrip)));
			//图标
			$icon = $info['img'];
			echo "本地图标:&nbsp;&nbsp;&nbsp;&nbsp;" .  $icon . '<br/>';
			array_push($page,  array("本地图标:" ,  $icon ));
			
			//应用截图
			echo "游戏截图列表:&nbsp;&nbsp;&nbsp;&nbsp;" . '<br/>';
			array_push($page,  array("游戏截图列表:"));
			echo "序号&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本地" . '<br/>';
			array_push($page, array("序号","本地"));
			list(, $gimgs) = Resource_Service_Img::getList(0, 20, array('game_id'=>$gid));
			foreach($gimgs as $key => $value){
				$imgs = array($key, $value['img']);
				echo implode("&nbsp;&nbsp;&nbsp;&nbsp;", $imgs) . '<br/>' ;
				array_push($page,  $imgs);
			}
			echo "游戏版本列表:&nbsp;&nbsp;&nbsp;&nbsp;" . '<br/>';
			array_push($page, array("游戏版本列表:"));
			echo "版本&nbsp;&nbsp;&nbsp;&nbsp;Version Code&nbsp;&nbsp;&nbsp;&nbsp;大小&nbsp;&nbsp;&nbsp;&nbsp;本地&nbsp;&nbsp;&nbsp;&nbsp;线上&nbsp;&nbsp;&nbsp;&nbsp;支持最低系统版本&nbsp;&nbsp;&nbsp;&nbsp;最小分辨率&nbsp;&nbsp;&nbsp;&nbsp;最大分辨率&nbsp;&nbsp;&nbsp;&nbsp;版本状态".'<br/>';
			array_push($page, array("版本","Version Code","大小","地址","支持最低系统版本","最小分辨率","最大分辨率","版本状态"));
			//应用版本
			$versions = Resource_Service_Games::getIdxVersionByGameId($gid);
			foreach ($versions as $value){
				$verData = array($value['version'] ,
						$value['version_code'] ,
						$value['size'] . "M" ,
						$value['link'] ,
						$sys_version[$value['min_sys_version']]['title'] ,
						$resolution[$value['min_resolution']]['title'] ,
						$resolution[$value['max_resolution']]['title'],
						($value['status'])?'上线':'下线'
				) ;
				echo implode("&nbsp;&nbsp;&nbsp;&nbsp;", $verData) . '<br/>' ;
				array_push($page, $verData);
			}
			//导出文件
			if($file){
				$filename = $info['id'] .'-'. $info['name'];
				$xls = new Util_Excel('UTF-8', false, $filename);
				$xls->addArray($page);
				$xls->generateXML($filename);
			}
			echo '<br/><br/><br/><br/><br/><br/>';
			echo "===============================游戏资料输出完毕=======================================";
    }
    
    private static function _gameData(array $source, array $data ,$type) {
    	$tmp = array();
    	foreach($data as $key=>$value){
    		$tmp[] = $source[$value[$type]]['title'];
    	}
    	return $tmp;
    }
    
    //获取二维数组指定 key的值
    private static function _getDataByKey($data, $key, $pre=''){
    	$tmp  = array();
    	foreach($data as $value){
    		$tmp[] = $pre.$value[$key];
    	}
    	return $tmp;
    }
    
    public static function _writeFile($filename, $page){
    	$saveFile = Common::getConfig('siteConfig', 'logPath') . '/doc/' . $filename. '.xls';
    	$xls = new Util_Excel('UTF-8', false, $filename);
    	$xls->addArray($page);
    	$xlsData = $xls->saveXML();
    	file_put_contents($saveFile, $xlsData);
		echo '成功写入'.$filename ."\r\n";
		sleep(20);
      }
}
<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class DevController extends Api_BaseController {
	
	public $language = array(
			'1' => "中文",
			'2' => "英文",
			'3' => "其他",
	);
	
    /**
     * 添加（上线）游戏
     */
    public function onAction() { 
    	header("Content-type: text/html; charset=utf-8");
    	$info = $this->getInput(array('id', 'sign'));
    	Common::log(array("appid: {$info['id']}", "sign: {$info['sign']}"), date('Y-m-d') . '_dev.on.log');
    	if(!$info['id'])  exit;
    	$log = array('act' => '1');
    	$params = array("id"=>$info['id']);
    	$rsa = new Util_Rsa();
    	if (!$info['id']) $this->output(-1, 'error dev appid', array()); 
    	$ret = $rsa->verifySign($params, $info['sign'], Common::getConfig("siteConfig", "rsaPubFile"));
    	if (!$ret) $this->output(-1, 'error token.', array());
    	
    	//获取数据
    	$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
        $url = $urls[1] . '?appid='.$info['id'];
    	$curl = new Util_Http_Curl($url);
    	$result = $curl->get();
    	$base_tmp =  $tmp = $category = $simg = $device = $labels = array();
    	$tmp = json_decode($result,true);
    	$result = $tmp['data'];
    	Common::log($result, date('Y-m-d') . '_dev.on.log');
    	//数据写入    	
    	if($result){
    		$log['app_id'] =  $result['appid'];
    		//游戏基本信息
    		$img = explode('|',$result['icon']);
    		$base_tmp = array(
    				'appid'=> $result['appid'],
    				'apkid'=> $result['apkid'],
    				'name'=> $result['name'],
    				'resume'=> htmlentities($result['resume']),
    				'label'=> htmlentities($result['label']),
    				'img'=> $img[0],
    				'mid_img'=> $img[1],
    				'big_img'=> $img[2],
    				'language'=> $this->language[$result['language']] ,
    				'package'=> $result['package'],
    				'packagecrc'=> crc32(trim($result['package'])),
    				'price'=> $result['price'],
    				'company'=> $result['author_name'],
    				'descrip'=> htmlentities($result['descrip']),
    				'tgcontent'=> htmlentities($result['tgcontent']),
    				'create_time'=> $result['create_time'],
    				'online_time' => $result['online_time'],
    				'status'=> ($result['status'] == 3 ? 1 : 0),//3为已上线，否则未上线
    				'hot'=> $result['hot'],
    				'cooperate'=> $result['cooperate'],
    				'developer'=> $result['developer'],
    				'certificate'=> $result['safeInfos'] ? serialize($result['safeInfos']) : '' ,
    				'secret_key'=> trim($result['secret_key']),
    				'api_key'=> trim($result['api_key']),
    				'agent'=> trim($result['agent']),
    				'webp'=> $result['webp']
    				);
    		
    		//游戏截图缩略图
    		$simg = explode('|',$result['imgs']);
    		//游戏分类-旧版
    		//$category = explode('-',$result['category']);
    		
    		//游戏主分类-v1.5.6
    		$category['mainParent'] = $result['category_p'];
    		$category['mainSub'] = $result['category_p_son'];
    		//游戏次分类-v1.5.6
    		$category['lessParent'] = $result['category_s'];
    		$category['lessSub'] = $result['category_s_son'];
    		    		
    		//游戏分辨率
    		$resolution = $result['resolution'];

    		//游戏标签属性
    		/**
       		 *network_type    [联网类型]
    		 *character       [游戏特色]
    		 *billing_model   [资费方式]
    		 *detail_category [详细分类]
    		 *level           [游戏评级]
    		 *about           [内容题材]
    		 *style           [画面风格]
    		 */
    		$labelConfig =  Common::getConfig("apiConfig", 'label');
    		$labelConfig = (ENV == 'product') ? $labelConfig['product'] : $labelConfig['test'];
    		
    		$network_type = $this->_getData($result['network_type'], $labelConfig['network_type']);
    		$character = $this->_getData($result['character'], $labelConfig['character']);
    		$detail_category = $this->_getData($result['detail_category'], $labelConfig['detail_category']);
    		$level = $this->_getData($result['level'], $labelConfig['level']);
    		$about = $this->_getData($result['about'], $labelConfig['about']);
    		$style = $this->_getData($result['style'], $labelConfig['style']);
    		
    		  		
    		//合并处理
    		$labels = array_merge($network_type, $character,$detail_category,$level,$about,$style);
    		//游戏版本信息
    		$versions = $result['versions'];
    		//游戏的查分信息
    		$diffpackages = $result['diffpackages'];
    	}
	
    	//检查游戏是否为新的
     	$game = Resource_Service_Games::getBy(array('package'=>trim($result['package']))); 
     	//游戏id或无
     	$log['game_id'] = $game ? $game['id'] : 0;
    	if($game)  {
    		//旧的就更新游戏
    		$ret = Dev_Service_Sync::updateGame($base_tmp, $simg, $game['id'], $category, $labels, $versions, $diffpackages, $resolution);
    	} else {
    		//新的游戏直接添加
    		$ret = Dev_Service_Sync::addNewGame($base_tmp, $simg, $category, $labels, $versions, $diffpackages, $resolution);
    	}
    	$log['message'] ="app:{$result['appid']}上线同步";
    	if ($ret) {
    		$log['status'] = 1;
    		//写入同步记录
    		Resource_Service_Sync::add($log);
    		echo 'ok';
    	}
    }
    
    /**
     * 关闭（下线）游戏
     * @return boolean
     */
    public function offAction() {
    	$info = $this->getInput(array('id', 'sign'));
    	Common::log(array("appid: {$info['id']}", "sign: {$info['sign']}"), date('Y-m-d') . '_dev.off.log');
    	if (!$info['id']) $this->output(-1, 'error dev appid', array());
    	if(!$info['id'])  exit;
    	$params = array("id"=>$info['id']);
    	$rsa = new Util_Rsa();
    	$ret = $rsa->verifySign($params, $info['sign'], Common::getConfig("siteConfig", "rsaPubFile"));
    	if (!$ret) $this->output(-1, 'error token.', array());
    	$log['app_id'] =  $info['id'];
    	//通过appid找到game_id
    	$game = Resource_Service_Games::getBy(array('appid'=>$info['id']));
    	if($game['status']) {
    		$log['game_id'] = $game['id'];
    		
    		//下线游戏操作
    		//已上线versionID
    		$version = Resource_Service_Games::getIdxVersionByResourceGameId(array('game_id'=> $game['id'], 'status' => '1'));
    		$vid = $version[0]['id'];
    		$version[0]['status'] = 0;
    		$ret = Dev_Service_Sync::offGame($version[0], $vid);
    		$log['message'] ="app:{$info['id']}下线同步";
    		if ($ret) {
    			$log['status'] = 1;
    			//写入同步记录
    			Resource_Service_Sync::add($log);
    			echo 'ok';
    		}
    	}
    }
    
    /**
     * appid转game_id
     * @return Ambigous <>
     */
    public function convertAction() {
    	$info = $this->getInput(array('id', 'sign'));
    	if(!$info['id'])  exit;
    	$params = array("id"=>$info['id']);
    	$rsa = new Util_Rsa();
    	$ret = $rsa->verifySign($params, $info['sign'], Common::getConfig("siteConfig", "rsaPubFile"));
    	if (!$ret) $this->output(-1, 'error token.', array());
    	//通过appid找到game_id
    	$game = Resource_Service_Games::getBy(array('appid'=>$info['id']));
    	if(!$game) exit;
    	echo $game['id'];
    }
    
    private  function _getData($data, $id) {
    	$tmp = array();
    	$labs = explode('|',$data);
    	foreach($labs as $key=>$value){
    		if($value){
    			$tmp[] = $id.'|'.$value;
    		}
    	}
    	return $tmp;
    }
}
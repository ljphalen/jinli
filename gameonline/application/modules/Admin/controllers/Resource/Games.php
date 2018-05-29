<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_GamesController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Games/index',
		'addUrl' => '/Admin/Resource_Games/add',
		'addPostUrl' => '/Admin/Resource_Games/add_post',
		'editUrl' => '/Admin/Resource_Games/edit',
		'editPostUrl' => '/Admin/Resource_Games/edit_post',
		'deleteUrl' => '/Admin/Resource_Games/delete',
		'deleteVersionUrl' => '/Admin/Resource_Games/deleteVersion',
		'deleteImgUrl' => '/Admin/Resource_Games/delete_img',
		'uploadUrl' => '/Admin/Resource_Games/upload',
		'uploadPostUrl' => '/Admin/Resource_Games/upload_post',
		'step3PostUrl' => '/Admin/Resource_Games/add_step3Post',
		'step2editUrl' => '/Admin/Resource_Games/edit_step2',
		'step3editUrl' => '/Admin/Resource_Games/edit_step3',
		'step4editUrl' => '/Admin/Resource_Games/edit_step4',
		'step5Url' => '/Admin/Resource_Games/edit_step5',
		'step6Url' => '/Admin/Resource_Games/edit_step6',
		'step5postUrl' => '/Admin/Resource_Games/post_step5',
		'step3postUrl' => '/Admin/Resource_Games/post_step3',
		'uploadImgUrl' => '/Admin/Resource_Games/uploadImg',
		'deleteDiffUrl' => '/Admin/Resource_Games/delete_diff',
		'newUrl' => '/Admin/Resource_Games/add_new',
		'batchUpdateUrl'=>'/Admin/Resource_Games/batchUpdate',
	);
	
	public $perpage = 20;
	
	//合作方式1:联运，2:普通
	public $cooperates = array(
			1 => '联运',
			2 => '普通',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if($page < 1) $page = 1;
		
		
		$params  = $this->getInput(array('name', 'id', 'status', 'category','type','company','cooperate','appid'));
		$search = $category_games = array();
		
		//分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$this->assign('categorys', $categorys);
		
		$search = $ids = $scategory = array();
		
		
		if ($params['name']) $search['name'] = $params['name'];
		if ($params['status']) $search['status'] = $params['status'] - 1;
		if ($params['id']) $search['id'] = $params['id'];
		if ($params['cooperate']) $search['cooperate'] = $params['cooperate'];
		if ($params['company']) $search['company'] = array('LIKE',$params['company']);
		if ($params['appid'] || $params['appid'] == "0" )  $search['appid'] = $params['appid'];
	
		
		
		
		if($params['category']){
			$scategory['category_id'] = array('IN', $params['category']);
			$category_games = Resource_Service_Games::getIdxGameResourceCategoryBy($scategory);
			$category_games = Common::resetKey($category_games, 'game_id');
        	$category_games = array_unique(array_keys($category_games));
        	if($category_games && $params['id']){
        		$category_games = array_intersect(array($params['id']),$category_games);
        		if($category_games) {
        			$search['id'] = array('IN',$category_games);
        		} else {
        			$search['id'] = 0;
        		}
        	} else if($category_games && !$params['id']){
        		$search['id'] = array('IN',$category_games);
        	} else {
        			$search['id'] = 0;
        	}
		}
		list($total, $games) = Resource_Service_Games::adminSearch($page, $this->perpage, $search, array('online_time'=>'DESC','id'=>'DESC'));
		$gamei_ds = Common::resetKey($games, 'id');
		
		$versions = Resource_Service_Games::getCountResourceVersion();
		$versions = Common::resetKey($versions, 'game_id');
        
        
        $new_versions = Resource_Service_Games::getIdxVersionByNewVersion();
        $new_versions = Common::resetKey($new_versions, 'game_id');
        
        $online_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
        $online_versions = Common::resetKey($online_versions, 'game_id');
		
        
        list(, $sys_version) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>5,'status'=>1));
        $sys_version = Common::resetKey($sys_version, 'id');
        $this->assign('sys_version', $sys_version);
        
        list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4,'status'=>1));
        $resolution = Common::resetKey($resolution, 'id');
        $this->assign('resolution', $resolution);
        
        list(, $price) = Resource_Service_Attribute::getList(1, 100 ,array('at_type'=>3,'status'=>1));
        $price = Common::resetKey($price, 'id');
        $this->assign('price', $price);
        
        //游戏评级
        $pj_id = ((ENV == 'product') ? 107 : 120);
        list(, $levels) = Resource_Service_Label::getList(1, 100,array('btype'=>$pj_id,'status'=>1));
        $levels = Common::resetKey($levels, 'id');
        $tmp = array();
        foreach($games as $k=>$val){
        	$tmp[$val['id']] = $levels[$val['level']]['title'];
        }
        $game_level = $tmp;
        
        $this->assign('versions', $versions);
        $this->assign('params', $params);
        $this->assign('new_versions', $new_versions);
        $this->assign('online_versions', $online_versions);
        $this->assign("type", $params['type']);
        $this->assign("cooperates", $this->cooperates);
        $this->assign("total", $total);
        $this->assign('game_level', $game_level);
        
		$this->cookieParams();
		$this->assign('games', $games);
		$url = $this->actions['listUrl'].'/?'. http_build_query($params).'&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		
		
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function enteringAction() {
		$this->forward('index', array('type'=>2));
		return FALSE;
	}
	
	
	/**
	 * add game page show
	 */
	public function edit_step4Action() {
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
		$game_id = intval($this->getInput('game_id'));
		$game_info = Resource_Service_Games::getResourceGames($game_id);
		$version_info = Resource_Service_Games::getIdxVersionByVersionId($id,$game_id);
		
		$packages = Resource_Service_Games::getIdxPackageByPackageId(array('version_id'=>$id,'game_id'=>$game_id));
		
		list(, $categorys) = Resource_Service_Attribute::getsortList(1, 150,array('at_type'=>1,'status'=>1));
		$categorys = Common::resetKey($categorys, 'id');
		
		$category_games = Resource_Service_Games::getGameResourceByCategoryByGameId($game_id);
		$category_title = array();
		foreach($category_games as $key=>$val){
			$category_title[] = $categorys[$val['category_id']]['title'];
		}
		$category_titles = implode(',' , $category_title);
		
	
		list(, $sys_version) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>5,'status'=>1));
		$sys_version = Common::resetKey($sys_version, 'id');
		$this->assign('sys_version', $sys_version);
	
		$online_info = Resource_Service_Games::getIdxVersionByVersionInfo($game_id);
		$this->assign('online_info', $online_info[0]);
		
		$info = Resource_Service_Games::getIdxVersionByVersionId(intval($id),intval($game_id));
		$this->assign('game_id', $game_id);
		$this->assign('id', $id);
		$this->assign('game_info', $game_info);
		$this->assign('version_info', $version_info);
		$this->assign('category_titles', $category_titles);
		$this->assign('info', $info);
		$this->assign('type', $type);
		$this->assign('packages', $packages);
	
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_step2Action() {
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
		$info = Resource_Service_Games::getResourceGames($id);
		list(, $categorys) = Resource_Service_Attribute::getsortList(1, 150,array('at_type'=>1,'status'=>1));
		$categorys = Common::resetKey($categorys, 'id');
		
		$category_games = Resource_Service_Games::getGameResourceByCategoryByGameId($id);
		$category_title = array();
		foreach($category_games as $key=>$val){
			$category_title[] = $categorys[$val['category_id']]['title'];
		}
		
		$category_titles = implode(',' , $category_title);
		
		list(, $sys_version) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>5,'status'=>1));
		$sys_version = Common::resetKey($sys_version, 'id');
		$this->assign('sys_version', $sys_version);
		
		list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4,'status'=>1));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
		
		list(, $price) = Resource_Service_Attribute::getList(1, 100 ,array('at_type'=>3,'status'=>1));
		$price = Common::resetKey($price, 'id');
		$this->assign('price', $price);
		

		$this->assign('id', $id);
		$this->assign('type', $type);
		$versions = Resource_Service_Games::getIdxVersionByGameId(intval($id));
		$packages = Resource_Service_Games::getIdxGameResourceDiff();
		$packages = Common::resetKey($packages, 'version_id');
		
		//获取当前每个版本上线时间和版本信息
		$tmp = $on_times = array();
		
		$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
		foreach($versions as $key=>$value){
			$url = $urls[2] . '?apkid='.$value['id'];
			$curl = new Util_Http_Curl($url);
			$result = $curl->get();
			$tmp = json_decode($result,true);
			$result = $tmp['data'];
			$on_times[$value['id']] = $result['online_time'];
		}		
		$this->assign('versions', $versions);
		$this->assign('on_times', $on_times);
		$this->assign('info', $info);
		$this->assign('category_titles', $category_titles);
		$this->assign('packages', $packages);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_step3Action() {
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
		$game_id = intval($this->getInput('game_id'));
		
		//游戏基本信息
		$game_info = Resource_Service_Games::getResourceGames($game_id);
		
		//游戏分类
		list(, $categorys) = Resource_Service_Attribute::getsortList(1, 150,array('at_type'=>1,'status'=>1));
		$categorys = Common::resetKey($categorys, 'id');
		
		//某个游戏分类
		$category_games = Resource_Service_Games::getGameResourceByCategoryByGameId($game_id);
		$category_title = array();
		foreach($category_games as $key=>$val){
			$category_title[] = $categorys[$val['category_id']]['title'];
		}
		$category_titles = implode(',' , $category_title);
		
	    //系统最小版本
		list(, $sys_version) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>5,'status'=>1));
		$sys_version = Common::resetKey($sys_version, 'id');
		$this->assign('sys_version', $sys_version);
	    
		//游戏分辨率
		list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4,'status'=>1));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
	    
		//游戏资费模式
		list(, $price) = Resource_Service_Attribute::getList(1, 100 ,array('at_type'=>3,'status'=>1));
		$price = Common::resetKey($price, 'id');
		$this->assign('price', $price);
	
		$category_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>intval($game_id)));
		$category_ids = self::_gameData($category_ids,'category_id');
		$this->assign('category_ids', $category_ids);
		
		//游戏支持设备
		$device_ids = Resource_Service_Games::getIdxGameResourceDeviceBy(array('game_id'=>intval($game_id)));
		$device_ids = Common::resetKey($device_ids,'device_id');
		$device_ids = array_unique(array_keys($device_ids));
		$this->assign('device_ids', $device_ids);
		
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$this->assign('categorys', $categorys);
		
		//游戏略缩图
		list(, $gimgs) = Resource_Service_Img::getList(0, 20, array('game_id'=>intval($game_id)));
		$this->assign('gimgs', $gimgs);
		

		//游戏属性
		list(, $hots) = Resource_Service_Attribute::getsortList(1, 150,array('at_type'=>7,'status'=>1));
		$hots = Common::resetKey($hots, 'id');
		$this->assign('hots', $hots);
		
		//游戏标签分类
		list(, $lab_categorys) = Resource_Service_Attribute::getList(1, 150,array('at_type'=>8,'status'=>1));
		$temp = array();
		foreach($lab_categorys as $key=>$value){
			//if($value['id'] != 107) $temp[] = $value; //线上
			if($value['id'] != 120) $temp[] = $value;   //测试
		}
		$lab_categorys = $temp;
		$lab_categorys = Common::resetKey($lab_categorys, 'id');
		//游戏标签
		list(, $labels) = Resource_Service_Label::getAllSortLabel();
		
		$tmp = array();
		foreach($labels as $key=>$value){
			//if($value['btype'] != 107) $tmp[$value['btype']][] = $value;  //线上
			if($value['btype'] != 120) $tmp[$value['btype']][] = $value;    //测试
		}
		$btype = $tmp;
		
		//游戏支持设备
		list(, $devices) = Resource_Service_Attribute::getList(1, 150,array('at_type'=>9,'status'=>1));
		$this->assign('devices', $devices);
		
		//游戏合作方式
		$this->assign('cooperates', $this->cooperates);
		
		$res_title = array();
		$resolut = Resource_Service_Games::getIdxGameResourceResolutionByGameId(array('game_id'=>$game_id,'status'=>1));
        foreach($resolut as $key=>$value){
        	$res = Resource_Service_Attribute::getResourceAttribute ( $value ['attribute_id'] );
        	$res_title[] = $res['title'];
        }
		
		$game_labels = Resource_Service_Games::getIdxLabelByGameId(intval($game_id));
		$game_labels = Common::resetKey($game_labels, 'label_id');
		$game_labels = array_unique(array_keys($game_labels));
		
		//游戏版本信息
		$info = Resource_Service_Games::getIdxVersionByVersionId(intval($id),intval($game_id));
		
		$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
		$apk_info = Dev_Service_Sync::getApkInfo($id,$urls[2]);
		$this->assign('apk_info', $apk_info);
		
		$this->assign('type', $type);
		$this->assign('lab_categorys', $lab_categorys);
		$this->assign('labels', $labels);
		$this->assign('btype', $btype);
		$this->assign('game_labels', $game_labels);
	
		
		
		$this->assign('game_id', $game_id);
		$this->assign('game_info', $game_info);
		$this->assign('category_titles', $category_titles);
		$this->assign('sys_version', $sys_version);
		$this->assign('resolution', $resolution);
		$this->assign('info', $info);
		$this->assign('id', $id);
		$this->assign('res_title', $res_title);
	}
	
	
	
	/**
	 *
	 * @param array $data
	 * @param unknown_type $type
	 * @return boolean|multitype:unknown
	 */
	private static function _gameData(array $data ,$type) {
		$tmp = array();
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$tmp[] = $value[$type];
		}
		return $tmp;
	}
	
	public function edit_step5Action() {
		$id = $this->getInput('id');
		$type = $this->getInput('type');
		//游戏评级
		$pj_id = ((ENV == 'product') ? 107 : 120);
		list(, $levels) = Resource_Service_Label::getList(1, 100,array('btype'=>$pj_id,'status'=>1));
		$game_label = Resource_Service_Games::getResourceGames(intval($id));
		$this->assign('levels', $levels);
		$this->assign('game_label', $game_label);
		$this->assign('id', $id);
		$this->assign('type', $type);
	}
	public function edit_step6Action() {
		$id = $this->getInput('id');
		$type = intval($this->getInput('type'));
		$game_id = intval($this->getInput('game_id'));
		
		//游戏基本信息
		$game_info = Resource_Service_Games::getResourceGames($game_id);
		$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
		$apk_info = Dev_Service_Sync::getApkInfo($id,$urls[2]);
		$this->assign('apk_info', $apk_info);
		$this->assign('type', $type);
		$this->assign('game_id', $game_id);
		$this->assign('game_info', $game_info);
		$this->assign('id', $id);
		$this->assign("cooperates", $this->cooperates);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function post_step3Action() {
		$info = $this->getPost(array('id','version_id','name','price','language','developer','resume','label', 'tgcontent', 'hot', 'descrip', array('changes', '#s_zb')));
		$category = $this->getPost('category');
		$labels = $this->getPost('labels');
		$labels = array_unique($labels);
		$device = $this->getPost('device');
		$ret = Resource_Service_Games::updateBaseResourceGames($info, $info['id'],$category,$labels,$device,$info['version_id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function post_step5Action() {
		$info = $this->getPost(array('id','level')); 
		$ret = Resource_Service_Games::updateResourceGames(array('level'=>$info['level']), $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$ret = Resource_Service_Games::deleteResourceGamesIdx(intval($id));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function delete_diffAction() {
		$id = $this->getInput('id');
		$ret = Resource_Service_Games::deleteIdxGameResourceDiff(intval($id));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function deleteVersionAction() {
		$id = $this->getInput('id');
		$version = Resource_Service_Games::getIdxGameResourceVersion(intval($id));
		if($version['status']){
			//更新库游戏
			Resource_Service_Games::updateResourceGamesStatus(0,$version['game_id']);
			//更新单机
			Client_Service_Channel::updateChannelStatus($version['game_id'],0,1);
			//更新网游
			Client_Service_Channel::updateChannelStatus($version['game_id'],0,2);
		}
		$ret = Resource_Service_Games::deleteIdxGameResourceVersion(intval($id));
		
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookDataBasic($info) {
		if(!$info['name']) $this->output(-1, '游戏名称不能为空.');
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['developer']) $this->output(-1, '开发者不能为空.');
		if(!$info['language']) $this->output(-1, '语言不能为空.');
		if(!$info['price']) $this->output(-1, '资费方式不能为空.');
		if(!$info['company']) $this->output(-1, '游戏来源不能为空.');
		if(!$info['package']) $this->output(-1, '包名不能为空.');
		if(!$info['img']) $this->output(-1, '图标不能为空.');
		if(!$info['descrip']) $this->output(-1, '内容不能为空.');
		return $info;
	}
	
	
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookDataPackage($info) {
		if(!$info['link']) $this->output(-1, '下载链接不能为空.');
		if(!$info['version_code']) $this->output(-1, 'Version Code不能为空.');
		if(!$info['md5_code']) $this->output(-1, 'MD5校验值不能为空.');
		if(!$info['version']) $this->output(-1, '版本号不能为空.');
		if(!$info['size']) $this->output(-1, '文件大小不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		return $info;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookDataDiffPackage($info) {
		if(!$info['link']) $this->output(-1, '下载链接不能为空.');
		if(!$info['object_id']) $this->output(-1, '拆分包对象版本ID.');
		if(!$info['diff_name']) $this->output(-1, '拆分包名称不能为空.');
		//if(!$info['new_version']) $this->output(-1, '新版本的VersionName不能为空.');
		//if(!$info['old_version']) $this->output(-1, '旧版本的VersionName不能为空.');
		if(!$info['size']) $this->output(-1, '文件大小不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		return $info;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function delete_imgAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Img::getGameImg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Resource_Service_Img::deleteGameImg($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function uploadAction() {
		$info = $this->getInput(array('imgId', 'mkthumb'));
		$this->assign('imgId', $info['imgId']);
		$this->assign('mkthumb', intval($info['mkthumb']));
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'resource');
		$imgId = $this->getPost('imgId');
		if ($this->getPost('mkthumb')) {
			$this->mkthumb($ret['data']);
		}
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
    public function mkthumb($file) {
    	$attachPath = Common::getConfig('siteConfig', 'attachPath');
    	$img = $attachPath . $file;
    	$sp = explode(".", basename($img));    	
    	$thumb = basename($img) . "_240x400";
    	
    	$image = new Util_Img($img);
    	$image->resize(240, 400, '', '', '', 98);
    	$ret = $image->save($thumb, dirname($img), $sp[1]);
    	$file =realpath(dirname($img)."/".$thumb.".".$sp[1]);
    	image2webp($file, $file.".webp");
    	if (!$ret) copy($img, $thumb);
    }
    
    public function uploadImgAction() {
    	$ret = Common::upload('imgFile', 'resource');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
    }
}

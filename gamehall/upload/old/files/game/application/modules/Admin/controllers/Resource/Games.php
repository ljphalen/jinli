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
		'updateTypeChangeUrl'=>'/Admin/Resource_Games/updateTypeChange',
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
		$params  = $this->getInput(array('action', 'name', 'id', 'status', 'category', 'subcategory', 'type', 'company', 'cooperate', 'appid'));
		$search = $versions = $new_versions = $online_versions = array();
		//一级分类列表(不包括全部游戏/最新游戏)
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1, 'status'=>1, 'editable' => 0));
		$this->assign('categorys', $categorys);
		//选择一级分类后子分类数据获取
		if($params['category']){
			$subCategory = Resource_Service_Attribute::getsBy(array('at_type'=>10, 'status'=>1, 'parent_id' => $params['category']));
			$this->assign('subCategory', $subCategory);
		}
		
		switch ($params['action']){
			case 1 : 
				//模糊匹配
				$search = $this->fuzzySearch($params);
				break;
			case 2 : 
			case 3 :
			case 4 :
				//精确匹配
			    $search = $this->clearSearch($params);
				break;
		}
		list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $search, array('online_time'=>'DESC','id'=>'DESC'));
		if($games){
			$games = Common::resetKey($games, 'id');
			$gameIds = array_keys($games);
		
			$versions = Resource_Service_Games::getCountResourceVersion(array('game_id'=> array('IN', $gameIds)));
			$versions = Common::resetKey($versions, 'game_id');
        
        	$new_versions = Resource_Service_Games::getIdxVersionByNewVersion(array('game_id'=> array('IN', $gameIds)));
        	$new_versions = Common::resetKey($new_versions, 'game_id');
        
        	$online_versions = Resource_Service_Games::getsByIdxVersions(array('game_id'=> array('IN', $gameIds),'status'=>1));
        	$online_versions = Common::resetKey($online_versions, 'game_id');
		}     
        $sys_version = Resource_Service_Attribute::getsBy(array('at_type'=>5,'status'=>1));
        $sys_version = Common::resetKey($sys_version, 'id');
        $this->assign('sys_version', $sys_version);
        
        $resolution = Resource_Service_Attribute::getsBy(array('at_type'=>4,'status'=>1));
        $resolution = Common::resetKey($resolution, 'id');
        $this->assign('resolution', $resolution);
        
        $price = Resource_Service_Attribute::getsBy(array('at_type'=>3,'status'=>1));
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
	 * 游戏内容库模糊检索
	 * @param array $params
	 * @return array
	 */
	private function fuzzySearch($params){
		$categoryGame = $searhCategory = $result = array();
		$searhCategory['status'] = 1;
// 		$searhCategory['game_status'] = 1;
		//基于分类检索
		if($params['category']){
			//一级分类下的所有
			$searhCategory['parent_id'] = $params['category'];
			if($params['subcategory']){
				//二级分类的数据
				$searhCategory['category_id'] = $params['subcategory'];
				$searhCategory['parent_id'] = $params['category'];
			}
			$categoryGame = Resource_Service_GameCategory::getsBy($searhCategory);
			if($categoryGame){
				$categoryGame = Common::resetKey($categoryGame, 'game_id');
				$categoryGame = array_unique(array_keys($categoryGame));
				$result['id'] = array('IN', $categoryGame);
			}else {
				$result['id'] = 0 ;
			}
		}
				
		if ($params['status']) $result['status'] = $params['status'] - 1;
		if ($params['cooperate']) $result['cooperate'] = $params['cooperate'];
		if ($params['company']) $result['company'] = array('LIKE', $params['company']);
		return $result;
	}
	
	/**
	 * 游戏内容库精确检索
	 * @param array $params
	 * @return array
	 */
	private function clearSearch($params){
		$result = array();
		switch ($params['action']){
			case 2 :
				if($params['name']){
					$result['name'] = array('LIKE', $params['name']);
				}
				break;
			case 3 :
				if($params['id']){
					$result['id'] = $params['id'];
				}
				break;
			case 4 :
				if($params['appid']){
					$result['appid'] = $params['appid'];
				}
				break;
		}
		return $result;
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
	 *
	 * 运营管理/版本管理
	 */
	public function edit_step2Action() {
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
		$info = Resource_Service_Games::getBy(array('id' => $id));
		
		$mainCategory = Resource_Service_GameCategory::getMainCategory($id);
		$this->assign('mainCategory', $mainCategory);
		$lessCategory = Resource_Service_GameCategory::getLessCategory($id);
		$this->assign('lessCategory', $lessCategory);
				
		$sys_version = Resource_Service_Attribute::getsBy(array('at_type'=>5,'status'=>1));
		$sys_version = Common::resetKey($sys_version, 'id');
		$this->assign('sys_version', $sys_version);
	
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
		$this->assign('packages', $packages);
	}
	
	
	/**
	 * 差分包管理
	 */
	public function edit_step4Action() {
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
		$gameId = intval($this->getInput('game_id'));
		$gameInfo = Resource_Service_Games::getBy(array('id' => $gameId));
		
		$mainCategory = Resource_Service_GameCategory::getMainCategory($gameId);
		$this->assign('mainCategory', $mainCategory);
		$lessCategory = Resource_Service_GameCategory::getLessCategory($gameId);
		$this->assign('lessCategory', $lessCategory);
		
		$versionInfo = Resource_Service_Games::getIdxVersionByVersionId($id,$gameId);
		$packages = Resource_Service_Games::getIdxPackageByPackageId(array('version_id'=>$id,'game_id'=>$gameId));
		
		
		$this->assign('gameId', $gameId);
		$this->assign('gameInfo', $gameInfo);
		$this->assign('id', $id);
		$this->assign('versionInfo', $versionInfo);
		$this->assign('packages', $packages);
		$this->assign('type', $type);
		
	
	}
	
	/**
	 * 版本管理中
	 * 编辑上线版本
	 */
	public function edit_step3Action() {
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
		$gameId = intval($this->getInput('game_id'));
		//游戏基本信息
		$gameInfo = Resource_Service_Games::getBy(array('id' => $gameId));
		$parentCategory = Resource_Service_GameCategory::getParentCategory();
		$this->assign('parentCategory', $parentCategory);
		
		//游戏主分类
		$mainCategory = Resource_Service_GameCategory::getMainCategory($gameId);
		$this->assign('mainCategory', $mainCategory);
		$mainSubCategory = Resource_Service_GameCategory::getSubCategory($mainCategory['parent']['id']);
		$this->assign('mainSubCategory', $mainSubCategory);
		//游戏次分类
		$lessCategory = Resource_Service_GameCategory::getLessCategory($gameId);
		$this->assign('lessCategory', $lessCategory);
		if($lessCategory){
			$lessSubCategory = Resource_Service_GameCategory::getSubCategory($lessCategory['parent']['id']);
			$this->assign('lessSubCategory', $lessSubCategory);
		}
		
		//游戏资费模式
		$price = Resource_Service_Attribute::getsBy(array('at_type'=>3,'status'=>1));
		$price = Common::resetKey($price, 'id');
		$this->assign('price', $price);
	
		
		//游戏支持设备
		$device_ids = Resource_Service_Games::getIdxGameResourceDeviceBy(array('game_id'=>intval($gameId)));
		$device_ids = Common::resetKey($device_ids,'device_id');
		$device_ids = array_unique(array_keys($device_ids));
		$this->assign('device_ids', $device_ids);
		
		
		//游戏略缩图
		list(, $gimgs) = Resource_Service_Img::getList(0, 10, array('type'=>0, 'game_id'=>intval($gameId)));
		$this->assign('gimgs', $gimgs);
		

		//游戏属性
		$hots = Resource_Service_Attribute::getsBy(array('at_type'=>7,'status'=>1), array('sort'=>'DESC'));
		$hots = Common::resetKey($hots, 'id');
		$this->assign('hots', $hots);
		
		//游戏标签分类
		$labelSpecialId = (ENV == 'product') ? 107 : 120;
		$lab_categorys = Resource_Service_Attribute::getsBy(array('at_type'=>8,'status'=>1));
		$temp = array();
		foreach($lab_categorys as $key=>$value){
			if($value['id'] != $labelSpecialId) $temp[] = $value; 
		}
		$lab_categorys = $temp;
		$lab_categorys = Common::resetKey($lab_categorys, 'id');
		$this->assign('lab_categorys', $lab_categorys);
		
		
		//游戏标签
		list(, $labels) = Resource_Service_Label::getAllSortLabel();
		$tmp = array();
		foreach($labels as $key=>$value){
			if($value['btype'] != $labelSpecialId) $tmp[$value['btype']][] = $value;  
		}
		$btype = $tmp;
		$this->assign('btype', $btype);
		
		
		//游戏支持设备
		$devices = Resource_Service_Attribute::getsBy(array('at_type'=>9,'status'=>1));
		$this->assign('devices', $devices);
		//游戏合作方式
		$this->assign('cooperates', $this->cooperates);
		
		//分辨率标题
		$res_title = array();
		$resolut = Resource_Service_Games::getIdxGameResourceResolutionByGameId(array('game_id'=>$gameId,'status'=>1));
        foreach($resolut as $key=>$value){
        	$res = Resource_Service_Attribute::getBy(array('id'=>$value ['attribute_id']));
        	$res_title[] = $res['title'];
        }
        $this->assign('res_title', $res_title);
        
        //游戏标签值
		$game_labels = Resource_Service_Games::getIdxLabelByGameId($gameId);
		$game_labels = Common::resetKey($game_labels, 'label_id');
		$game_labels = array_unique(array_keys($game_labels));
		$this->assign('game_labels', $game_labels);
		
		//游戏版本信息
		$versionInfo = Resource_Service_Games::getIdxVersionByVersionId($id, $gameId);
		//开发者平台版本信息
		$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
		$apk_info = Dev_Service_Sync::getApkInfo($id,$urls[2]);
		$this->assign('apk_info', $apk_info);
		
		$this->assign('type', $type);
		$this->assign('gameId', $gameId);
		$this->assign('gameInfo', $gameInfo);
		$this->assign('versionInfo', $versionInfo);
		$this->assign('id', $id);
		
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
	
	/**
	 * 查看版本数据
	 */
	public function edit_step6Action() {
		
		$id = intval($this->getInput('id'));
		$type = intval($this->getInput('type'));
		$gameId = intval($this->getInput('game_id'));
		//游戏基本信息
		$gameInfo = Resource_Service_Games::getBy(array('id' => $gameId));
		//游戏主分类
		$mainCategory = Resource_Service_GameCategory::getMainCategory($gameId);
		$this->assign('mainCategory', $mainCategory);
		$mainSubCategory = Resource_Service_GameCategory::getSubCategory($mainCategory['parent']['id']);
		$this->assign('mainSubCategory', $mainSubCategory);
		//游戏次分类
		$lessCategory = Resource_Service_GameCategory::getLessCategory($gameId);
		$this->assign('lessCategory', $lessCategory);
		if($lessCategory){
			$lessSubCategory = Resource_Service_GameCategory::getSubCategory($lessCategory['parent']['id']);
			$this->assign('lessSubCategory', $lessSubCategory);
		}
		
		//开发者平台版本信息
		$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
		$apk_info = Dev_Service_Sync::getApkInfo($id,$urls[2]);
		$this->assign('apk_info', $apk_info);
		
		//游戏合作方式
		$this->assign('cooperates', $this->cooperates);
		$this->assign('type', $type);
		$this->assign('gameId', $gameId);
		$this->assign('gameInfo', $gameInfo);
		$this->assign('id', $id);
		
	}
	
	/**
	 *
	 * 提交上线版本编辑数据
	 */
	public function post_step3Action() {
		$info = $this->getPost(array('id','version_id','name','price','language','developer','resume','label', 'tgcontent', 'hot', 'descrip', array('changes', '#s_zb')));
		$category = $this->getPost(array('mainParent', 'mainSub', 'lessParent', 'lessSub'));
		$category = $this->cookCategory($category);
		$labels = $this->getPost('labels');
		$labels = array_unique($labels);
		$device = $this->getPost('device');
		$ret = Dev_Service_Sync::updateBaseResourceGames($info, $info['id'],$category,$labels,$device,$info['version_id']);
		if (!$ret) $this->output(-1, '操作失败.');
		if ($ret) {
			$this->_changeDataNotify($info['id'], 1);
			Client_Service_Ad::updateRecommendListVersionByGameId($info['id']);
		}
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
    
    /**
     * 通过一级分类id获取该分类的二级分类ID
     */
    public function getSubCategoryAction(){
    	$parentId = $this->getInput('id');
    	$subCategory = Resource_Service_GameCategory::getSubCategory($parentId);
    	$this->output(0,'',$subCategory);
    }
    
    /**
     * 分类过滤处理
     * @param array $info
     */
    private function cookCategory($info){
    	if(!$info['mainParent']) $this->output(-1, '主分类不能为空.');
    	if(!$info['mainSub']) $this->output(-1, '主分类中子分类不能为空.');
    	if($info['lessParent']&& (!$info['lessSub'])) $this->output(-1, '次分类中子分类不能为空.');
    	return $info;
    }
    
    /**
     * 游戏缓存变更通知
     * @param int $gameId
     * @param int $status
     *
     */
    private function _changeDataNotify($gameId ,$status){
    	Resource_Service_Games::notifyCache($gameId ,$status);
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updteListItem', $gameId);
        Async_Task::execute('Async_Task_Adapter_CategoryList', 'updateIndex', $gameId);
    }
    
    public function updateTypeChangeAction() {
    	$id = intval($this->getInput('id'));
    	$gameId = intval($this->getInput('game_id'));
    	$data = Resource_Service_Games::getIdxVersionByVersionId($id, $gameId);
    	if ($data['update_type'] == 1) {
	    	$update_type = 0;
    	} else {
    		$update_type = 1;
    	}

    	$result = Resource_Service_Games::updateIdxGameResourceVersionUpdateType($update_type, $id);

    	$ret['id'] = $gameId;
		if (!$result) $this->output(-1, $id, $ret);
		$this->output(0, '操作成功', $ret);
    }
    
}

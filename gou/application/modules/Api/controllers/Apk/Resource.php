<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 购物大厅应用推荐
 * @author huangsg
 *
 */
class Apk_ResourceController extends Api_BaseController {
	
	public $perpage = 16;
    /**
     * 
     */
    public function listAction() {
    	$version = intval($this->getInput('version'));
    	$server_version = Gou_Service_Config::getValue('Resource_Version');
    	if ($version == $server_version){	//数据版本一致则不返回数据
    		$this->output(0, '', array());
    	}
    	
    	$page = intval($this->getInput('page'));
    	$perpage = intval($this->getInput('perpage'));
    	if ($perpage) $this->perpage = $perpage;
    	
    	if ($page < 1) $page = 1;
    	list($total, $result) = Gou_Service_Resource::getList($page, $this->perpage, array('status'=>1)); 
    	$tmp = array();
    	$webroot = Common::getWebRoot();
    	foreach($result as $key=>$value) {
    		$imgList = Gou_Service_ResourceImg::getResourceImg($value['id']);
    		foreach ($imgList as $k=>$v){
    			$imgList[$k]['url'] = Common::getAttachPath() . $v['img'];
    			unset($imgList[$k]['img']);
    		}
    		
    		$tmp[] = array(
    			'id'=>$value['id'],
    			'name'=>html_entity_decode($value['name']),
    			'resume'=>html_entity_decode($value['resume']),
    			'version'=>$value['version'],
    			'version_name'=>$value['version_name'],
    			'description'=>html_entity_decode($value['description']),
    			'size'=>$value['size'] * 1024,
    			'company'=>html_entity_decode($value['company']),
    			'package'=>$value['package'],
    			'apk_md5'=>$value['md5_sign'],
    			'link'=>html_entity_decode($value['link']),
    			'icon'=>Common::getAttachPath() .$value['icon'],
    			'imgs'=>$imgList,
    		);
    	}

    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, "version"=>$server_version));
    }
    
    public function detailAction(){
    	$id = intval($this->getInput("id"));
    	if (empty($id)){
    		$this->output(1, '没有获取到ID', null);
    	}
    	
    	$appInfo = Gou_Service_Resource::get($id);
    	if (empty($appInfo)){
    		$this->output(1, '没有找到应用数据', null);
    	}
    	$appInfo['resume'] = html_entity_decode($appInfo['resume']);
    	$appInfo["imgs"] = Gou_Service_ResourceImg::getResourceImg($id);
    	unset($appInfo['ptype'], $appInfo['status'], $appInfo['hits'], $appInfo['sort']);
    	$this->output(0, '', $appInfo);
    }
    
    public function dtjAction() {
    	$id = intval($this->getInput('id'));
    	$type = $this->getInput('type');
    	$url = html_entity_decode(urldecode($this->getInput('_url')));
    	if (!id || !$type) return false;
    	switch ($type)
    	{
    		case RESOURCE:
    			Gou_Service_Resource::updateTJ($id);
    			break;
    		default:
    	}
    	
    	$this->redirect($url);
    }
    
    public function tjAction() {
    	$this->output(0, '');
    }
    
}

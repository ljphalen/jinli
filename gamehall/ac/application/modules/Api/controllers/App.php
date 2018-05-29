<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author lichanghua
 *
 */
class AppController extends Api_BaseController{
    public $perpage = 10;

    public function moreAction() {
    	$webroot = Common::getWebRoot();
        $page = intval($this->getInput('page'));
        $cku = $this->getInput('cku');
        $chl = $this->getInput('chl');
        if($page < 1) $page = 1;
        $perpage = $this->perpage;
        //获取装机必备分类id
        $config = Common::getConfig('appsConfig');
        $category= $config['system'];
        list($total, $apps) =  Resource_Service_IdxAppsCategory::getList($page, $perpage, array('category_id'=>$category, 'status'=>1), array('sort'=>'desc', 'create_time'=>'desc', 'id' => 'desc'));

        $tmp = array();
        foreach ($apps as $key => $value) {
        	$data = Resource_Service_Apps::get($value['app_id']);
            $tmp[] = array(
                'id'=>$data['id'],
                'name'=>$data['name'],
                'resume'=>$data['resume'],
                'score'=>$data['score'],
                'size'=>$data['size'].'M',
            	'detailUrl'=>Common::detailurl($cku, $chl, 'zjbblist', $webroot.'/front/app/info?id='.$data['id']),
                'link' => Common::dlurl("/front/app/dl", $data['id'], $cku, $chl, 'zjbblist', Common::getDownloadPath() . $data['link']),
                'icon'=>Common::getAttachPath() . $data['icon'],
            );
        }
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
}

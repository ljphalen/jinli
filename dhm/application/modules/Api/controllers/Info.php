<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class InfoController extends Api_BaseController {
	
	public $perpage = 20;
	
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $type = $this->getInput('type');
        if ($page < 1) $page = 1;

        //condition
        $condition['start_time'] = array(array('<=',time()));
        $condition['status'] = 1;
        $condition['type']   = $type;

        $sort = array('is_recommend'=>'DESC','sort'=>'DESC','start_time'=>'DESC');

        list($total, $infos) = Dhm_Service_Info::getList($page, $this->perpage, $condition, $sort);
        if($total == 0){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => 1));
        }
        
        $data = array();
        $webroot = Common::getWebRoot();
        $attachPath = Common::getAttachPath();
        foreach ($infos as $k=>$v) {
            //返回
            if($type) {
                $refer = urlencode($webroot . '/info/guides/');
            } else {
                $refer = urlencode($webroot . '/info/');
            }
            $data[$k]['id'] = intval($v['id']);
            $data[$k]['title'] = html_entity_decode($v['title']);
            $data[$k]['summary'] = html_entity_decode($v['summary']);
            $data[$k]['url'] = $webroot . '/info/detail?id=' . $v['id'].'&refer='.$refer;
            $data[$k]['date'] = date('m-d',$v['start_time']);
            $data[$k]['is_hot'] = $v['is_recommend'] ? true : false;
            
            $imgs = array();
            if($v['images']) {
                $v['images'] = explode(",", $v['images']);
                foreach ($v['images'] as $img) {
                    $imgs[] = sprintf('%s%s', $attachPath, $img);
                }
            }
            $data[$k]['images'] = $imgs;
        }
        
        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $data, 'hasnext' => $hasnext, 'curpage' => $page));
    }
}
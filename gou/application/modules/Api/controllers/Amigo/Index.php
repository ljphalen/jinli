<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author tiansh
 *
 */
class Amigo_IndexController extends Api_BaseController {
	
	public $actions =array(
			'tjUrl'=>'/index/tj'
	);
	
	public $perpage = 10;
		
	/**
	 * 商品列表
	 *
	 */
	public function goods_listAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		list($total, $goods) = Gou_Service_LocalGoods::getCanUseLocalGoods($page, $this->perpage, array('status'=>1, 'show_type'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
		
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['price'] = $value['price'];
			//$data[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_460x460.'.end(explode(".",  $value['img']));
			$data[$key]['img'] = $value['img_thumb'] ? Common::getAttachPath() . $value['img_thumb'] : Common::getAttachPath() . $value['img'];
			$data[$key]['link'] =  $webroot.'/amigo/goods/detail?id='.$value['id'].'&t_bi='.$this->t_bi;
			$data[$key]['title'] = html_entity_decode($value['title']);
			$data[$key]['short_desc'] = html_entity_decode($value['short_desc']);
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	
	/**
	 * 活动列表
	 *
	 */
	public function activity_listAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		list($total, $activitys) = Amigo_Service_Activity::getList($page, $this->perpage, array('status'=>1,'type'=>0, 'start_time'=>array('<', Common::getTime()), 'end_time'=>array('>', Common::getTime())),array('sort'=>'DESC', 'id'=>'DESC'));
	
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($activitys as $key=>$value) {
			$data[$key]['img'] = Common::getAttachPath()  .$value['img'];
			$data[$key]['link'] = $webroot.'/amigo/activity/redirect?id='.$value['id'];
			$data[$key]['title'] = html_entity_decode($value['title']);
			$data[$key]['start_time'] = date('y.m.d', $value['start_time']);
			$data[$key]['end_time'] = date('y.m.d', $value['end_time']);
			$data[$key]['is_end'] = $value['end_time'] < Common::getTime() ? true : false;
		}
        $data = array_values($data);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}

    /**
     *魅货
     */
    public function activity_longAction() {

		list($total, $activity) = Amigo_Service_Activity::getsby(array('status'=>1,'type'=>1),array('sort'=>'DESC', 'id'=>'DESC'));
        list(,$tag) = Amigo_Service_Tag::getAllTag();
        $tag = Common::resetKey($tag,'id');
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($activity as $key=>$value) {

            $item['img'] = Common::getAttachPath()  .$value['img'];
            $item['link'] = $webroot.'/amigo/activity/redirect?id='.$value['id'];
            $item['title'] = html_entity_decode($value['title']);
            $data[$value['tag_id']][]=$item;
		}
        $ret = array();
        foreach ($tag as $k=>$v) {
            if(empty($data[$k])) continue ;
            $ret[$k]['tag']=$v['name'];
            $ret[$k]['list']=array_values($data[$k]);
        }

        $data = array_values($ret);
		$this->output(0, '', array('list'=>$data,));
	}

}

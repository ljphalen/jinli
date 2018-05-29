<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 官网新闻接口
 *
 */
class NewsController extends Api_BaseController {
	
	public $actions = array(
				'navNewsUrl'=>'/api/news/navNews',
				'jhNewsUrl'=>'/api/news/jhNews',	
				'tjUrl'=>'/index/tj'
			);
	
	public function navNewsAction() {
	    header("Content-type:text/json");
		//获取6条导航新闻
		list(,$news) = Gou_Service_News::getCanUseNews(1, 6, array('type_id'=>2), array('pub_time'=>'DESC', 'sort'=>'DESC')); 
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($news as $key=>$value) {
			$data[$key]['id'] = $value['id'];
			$data[$key]['title'] = html_entity_decode($value['title']);
			$data[$key]['link'] =  Gou_Service_News::getShortUrl(Stat_Service_Log::V_H5, $value);
			$data[$key]['pubDate'] = date('Y-m-d H:i:s', $value['pub_time']);
			$data[$key]['sort'] = $value['sort'];
		}
		echo json_encode(array('channel'=>'购物', 'data'=>$data));
	}
	
	public function jhNewsAction() {
	    header("Content-type:text/json");
		//$page = intval($this->getInput('page'));
		//if ($page < 1) $page = 1;
		$page = 1;
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		
		list(, $pic_news) = Gou_Service_News::getCanUseNews($page, 2, array('type_id'=>1, 'img'=>array('!=', '')), array('sort'=>'DESC', 'id'=>'DESC'));
		
		list(, $news) = Gou_Service_News::getCanUseNews($page, 12, array('type_id'=>1, 'img'=>''), array('sort'=>'DESC', 'id'=>'DESC'));
		
		$imgs1 = array();
		$imgs2 = array();
		$imgs1['title'] = Util_String::substr($pic_news[0]['title'], 0, 14, '', true);
		$imgs1['img'] = Common::getAttachPath().$pic_news[0]['img'];
		$imgs1['link'] = $pic_news[0]['link'];
		
		$imgs2['title'] = Util_String::substr($pic_news[1]['title'], 0, 14, '', true);
		$imgs2['img'] = Common::getAttachPath().$pic_news[1]['img'];
		$imgs2['link'] = $pic_news[1]['link'];
		//$cache_img[] = $staticroot.$value['img'];
		
		$tex_news1 = array();
		$tex_news2 = array();
		foreach ($news as $key=>$val) {
			if($key <= 5){
				$tex_news1[$key]['title'] = Util_String::substr($val['title'], 0, 13, '', true);
				$tex_news1[$key]['category'] = Util_String::substr($val['category'], 0, 8, '', true);
				$tex_news1[$key]['link'] = $val['link'];
			} else {
				$tex_news2[$key-6]['title'] = Util_String::substr($val['title'], 0, 13, '', true);
				$tex_news2[$key-6]['category'] = Util_String::substr($val['category'], 0, 8, '', true);
				$tex_news2[$key-6]['link'] = $val['link'];
			}
		}
		
		//$this->cache($cache_img, 'news');
		//$hasnext = ($page==1 && $pic_total>1 && $total >= 12) ? true : false;
		$this->output(0, '', array('list'=>array(0=>array('pic'=>$imgs1, 'news'=>$tex_news1), 1=>array('pic'=>$imgs2, 'news'=>$tex_news2))));
	}
}

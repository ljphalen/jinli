<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_FanfanController extends Api_BaseController {

	public function indexAction() {
		$rs = Fanfan_Service_Topic::getTodayTopic();
		$webroot = Common::getWebRoot();
		$data = array();
		if (!empty($rs)){
			foreach ($rs as $val){
				$data[] = array(
					'id'=>$val['id'],
					'title'=>$val['title'],
					'subtitle'=>'',
					'resume'=>$val['topic_desc'],
					'img'=>Common::getAttachPath() . $val['img'],
					'source'=>'购物大厅',
					'out_link'=>$webroot. '/fanfan?id=' . $val['id'],
					'create_time'=>$val['time_line']
				);
			}
		}
		return $this->output(0, '', array('list'=>$data));
	}	
}

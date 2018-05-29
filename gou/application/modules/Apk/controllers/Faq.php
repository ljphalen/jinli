<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author ryan
 *
 */
class FaqController extends Apk_BaseController {
	
    /**
     * 问题详情
     * @return json
     */
    public function viewAction(){
        $id = $this->getInput('id');
        $row  = Cs_Service_Question::get($id);
        $item['id'] = $row['id'];
        $item['question'] = $row['question'];
        $item['answer'] = html_entity_decode($row['answer']);
        $this->assign('data', $item);
        $this->assign('title', '查看解决方案');
    }
}

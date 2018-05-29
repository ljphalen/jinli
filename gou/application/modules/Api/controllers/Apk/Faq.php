<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_FaqController extends Api_BaseController {
	
	public $perpage = 10;

    public function categoryAction()
    {
        list(, $category) = Cs_Service_QuestionCategory::getsBy(array('status'=>1), array('sort' => 'desc'));
        $cids = array_filter(array_keys(Common::resetKey($category, 'id')));
        $count = Cs_Service_Question::countByGroup(array('cat_id' => array('IN', $cids)), 'cat_id');
        $count = Common::resetKey($count,'cat_id');
        foreach ($category as $k=>&$c) {
            $c['icon'] = Common::getAttachPath() . $c['icon'];
            if (empty($count[$c['id']]['num'])) {
                unset($category[$k]);
            }
        }
        $this->output(0, '', array('list' => array_values($category)));
    }

    public function questionsAction()
    {
        $cid = $this->getInput('cid') ? $this->getInput('cid') : 1;
        $page = intval($this->getInput('page'));
        $perpage = $this->getInput('perpage') ? intval($this->getInput('perpage')) : $this->perpage;
        if ($page < 1) $page = 1;
        list($total, $result) = Cs_Service_Question::getsBy(array('cat_id' => $cid, 'status'=>1), array('sort' => 'desc'));
        $items = array();
        foreach ($result as $k => $v) {
            $item['id'] = $v['id'];
            $item['cid'] = $v['cat_id'];
            $item['question'] = $v['question'];
            $item['url'] = Common::getWebRoot() . '/faq/view?id=' . $v['id'];
            $items[] = $item;
        }
        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $items, 'hasnext' => $hasnext, 'curpage' => $page));
    }

}

<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GameController extends Api_BaseController {
	//主分类属性表at_type:取值为1
	public $parentCategoryType = 1;
	//子分类属性表at_type:取值为10
	public $subCategoryType = 10;
	
	/**
	 * 获取游戏分类子分类信息
	 * v1.5.6 开始
	 */
	public function categoryAction(){
		$data = array();
		$parentCategory = Resource_Service_Attribute::getsBy(array('at_type' => $this->parentCategoryType, 'status' => 1, 'editable' => 0), array('sort'=>'DESC'));
		if($parentCategory){
			foreach ($parentCategory as $item){
				$subCategory = Resource_Service_Attribute::getsBy(array('at_type'=>$this->subCategoryType, 'parent_id'=> $item['id'], 'status' => 1), array('sort'=>'DESC'));
				$items= array();
				if($subCategory){
					foreach ($subCategory as $value){
						$items[] = array(
							'id' => $value['id'],
							'title' => $value['title']
						);
					}
				}
				$data[]=array(
						'id' => $item['id'],
						'title' => $item['title'],
						'items' => $items
				);
			}
		}
		$this->output(0, '', $data);
	}
	
    /**
     * 
     */
    public function attributeAction() {
    	$sid = $this->getInput('sid');
    	if (!$sid) $this->output(-1, 'invalid sid.', array());
    	$params = array('at_type'=>$sid, 'status' => '1');
    	//分类属性特殊处理
    	if($sid == '1') $params['editable'] = 0;
    	$result = Resource_Service_Attribute::getsBy($params);
    	
    	$tmp = array();
    	foreach($result as $key=>$value) {
    		$tmp[] = array('id'=>$value['id'], 'title'=>$value['title'], 'status'=>$value['status']);
    	}
    	$this->output(0, '', $tmp);
    }
    
    public function labelAction() {
    	$sid = $this->getInput('lid');
    	if (!$sid) $this->output(-1, 'invalid lid.', array());
    	 
    	list(, $result) = Resource_Service_Label::getList(0,500, array('btype'=>$sid,'status' => '1'));
    	 
    	$tmp = array();
    	foreach($result as $key=>$value) {
    		$tmp[] = array('id'=>$value['id'], 'title'=>$value['title'], 'status'=>$value['status']);
    	}
    	$this->output(0, '', $tmp);
    }
    
    /**
     * 查询游戏信息
     */
    public function getGameInfoAction() {
        $authcode = $this->getInput('authcode');
        if ($authcode != '12e61fd6703954cf626a85b53e24bbcc') $this->clientOutput(array('code' => 0));
        $name = $this->getInput('name');
        $package = $this->getInput('package');
        $timestamp = intval($this->getInput('timestamp'));
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;
        $params = array();
        if ($name) $params['name'] = array('LIKE', $name);
        if ($package) $params['package'] = $package;
        if ($timestamp) $params['online_time'] = array('>', $timestamp);

        $perpage = 10;
        list($total, $list) = Resource_Service_Games::getGameInfo($params, $page, $perpage);
        if (!$list) $this->clientOutput(array('code' => 0));
        $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
  
        $data = array();
        foreach ($list as $value) {
            $data[] = array(
                'name' => $value['name'],
                'package' => $value['package'],
                'create_time' => $value['create_time'],
                'online_time' => $value['online_time'],
            );
        }

        $tmp = array(
            'code' => 1,
            'total' => $total,
            'hasnext' => $hasnext,
            'data' => $data,
        );
        $this->clientOutput($tmp);
    }

}
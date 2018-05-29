<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class TypeController extends Apk_BaseController {
	
	public function indexAction(){
		
	    //hash
        $type_taobao_search =  json_decode(Gou_Service_Config::getValue('type_taobao_search'), true);
        $action = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_APK, $type_taobao_search['module_id'],
                $type_taobao_search['channel_id'], 0, $type_taobao_search['url'], 'apk分类搜索', $type_taobao_search['channel_code']);
        
        $keyword = Gou_Service_Config::getValue('gou_client_keyword');
        
        //type
        list(, $pType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
        $pType = Common::resetKey($pType, 'id');
        list($total, $cType)  = Type_Service_Ptype::getsBy(array('pid'=>array('IN',array_keys($pType))));
        $static_root = Common::getAttachPath() ;
        $web_root = Common::getWebRoot();
        $cType = Common::resetKey($cType,'pid');

        foreach ($pType as $key=>$value) {
            if(empty($cType[$value['id']])) continue;
            $data[] = array(
              'id' => $value['id'],
              'name' => html_entity_decode($value['name']),
              'icon' => $static_root . $value['icon'],
              'link' => $web_root . '/api/apk_type/type?id=' . $value['id'],
            );
        }

		$this->assign('keyword', $keyword);
		$this->assign('action', $action);
		$this->assign('ptype', $data);
		$this->assign('title', '分类');
	}
}
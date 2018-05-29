<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 *
 */
class Apk_TypeController extends Api_BaseController {
    
    /**
     * 关键字
     */
    public function searchAction() {
        $keyword = Gou_Service_Config::getValue('gou_client_keyword');
    
        //hash
        $apk_taobao_search =  json_decode(Gou_Service_Config::getValue('type_taobao_search'), true);
        $action = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_APK, $apk_taobao_search['module_id'],
                $apk_taobao_search['channel_id'], 0, $apk_taobao_search['url'], '分类搜索', $apk_taobao_search['channel_code']);
    
        $this->output(0, '',  array('input_name'=>'keyword', 'keyword'=>$keyword, 'taobao_search_url'=>$action));
    }
    
	
	/**
	 * 大分类
	 * 
	 */
	public function ptypeAction() {
		//type
        $version = intval($this->getInput('version'));
        $server_version = Gou_Service_Config::getValue('Type_Version');

        if ($version == $server_version) $this->output(-1, '');
        //type
        $data = $this->_getTop('type');

        $this->output(0, '', array('type'=>$data, 'version'=>$server_version));
	}




	/**
	 * 子分类
	 *
	 */
	public function typeAction() {
        $cid = intval($this->getInput('id'));

        $page = 1;
        $perpage = 50;

        $version = intval($this->getInput('version'));
        $server_version = Gou_Service_Config::getValue('Type_Version') + Gou_Service_Config::getValue('Ad_Version');
        if ($version == $server_version) $this->output(-1, '');

        $type_data = $ad_data = array();
        $total = 0;

        //广告
        $ad_data = $this->_getAd($cid,2);
        if(!empty($ad_data))$ad_data=array($ad_data);

        list($child_data,$recommend) = $this->_getData($cid);
        if(!empty($recommend)) array_unshift($child_data,$recommend);
        $type_data['list'] = $child_data;

        $this->output(0, '', array('type_data'=>$type_data, 'ad_data'=>$ad_data, 'version'=>$server_version));
    }

    /**
     * 大分类  本地化接口(New)
     *
     */
    public function bigAction() {
        $version = intval($this->getInput('version'));
        $server_version = Gou_Service_Config::getValue('Type_Version');

        $pType_data = array();
        if ($version >= $server_version) $this->output(-1, '');
        //type
        $data = $this->_getTop('list');

        $this->output(0, '', array('type'=>$data, 'version'=>$server_version));
    }

    /**
     * 子分类   本地化接口(new )
     *
     */
    public function listAction() {
        $cid = intval($this->getInput('id'));

        $version = intval($this->getInput('version'));
        $server_version = Gou_Service_Config::getValue('Type_Version') + Gou_Service_Config::getValue('Ad_Version');

        if ($version >= $server_version) $this->output(-1, '');

        $type_data = $ad_data = array();
        $total = 0;

        list($child_data,$recommend) = $this->_getData($cid);
        if(!empty($recommend)) array_unshift($child_data,$recommend);
        $type_data['list'] = $child_data;
        //广告
        $ad_data = $this->_getAd($cid,2);

        $this->output(0, '', array('type_data'=>$type_data, 'ad_data'=>$ad_data, 'version'=>$server_version));
    }

	/**
	 * 大分类  本地化接口
	 *
	 */
	public function parentAction() {
	    $version = intval($this->getInput('version'));
	    $server_version = Gou_Service_Config::getValue('Type_Version');
	    
	    //ksort($pType);
	    $pType_data = array();
	    if ($version == $server_version) $this->output(-1, '');
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;

        $webroot = Common::getWebRoot();
        //type
        //获取主分类
        list($total, $pType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0),array('sort'=>'DESC','id'=>'DESC'));
        $pType = Common::resetKey($pType, 'id');
        //获取主分类 获取子分类
        list($total, $type)  = Type_Service_Type::getsBy(array('type_id'=>array('IN',array_keys($pType))));

        $static_root = Common::getAttachPath() ;
        $web_root = Common::getWebRoot();
        $type = Common::resetKey($type,'type_id');
        $data = array();
        foreach ($pType as $key=>$value) {
            if(empty($type[$value['id']])) continue;
            $data[] = array(
              'id' => $value['id'],
              'name' => html_entity_decode($value['name']),
              'icon' => $static_root . $value['icon'],
              'link' =>sprintf('%s/api/apk_type/%s?id=%d',$web_root,'child',$value['id'])
            );
        }

        $recommend=array('name'=>'推荐', 'icon'=>$staticroot.'/gou/pic', 'link'=>$webroot.'/api/apk_type/child?id=0');
        array_unshift($data,$recommend);
	    $this->output(0, '', array('type'=>$data, 'version'=>$server_version));
	}

	/**
	 * 子分类   本地化接口
	 *
	 */
	public function childAction() {
	    $cid = intval($this->getInput('id'));

	    $page = 1;
	    $perpage = 50;

	    $version = intval($this->getInput('version'));
	    $server_version = Gou_Service_Config::getValue('Type_Version') + Gou_Service_Config::getValue('Ad_Version');

	    $type_data = $ad_data = array();
	    $total = 0;
	    if ($version == $server_version) $this->output(-1, '');
        if($cid) {
            $params = array('status'=>1, 'type_id'=>$cid);
        } else {
            $params = array('status'=>1, 'is_recommend'=>1);
        }

        list($total, $types) = Type_Service_Type::getList($page, $perpage, $params);

        //广告
        if($page == 1) {
            $ad_data=$this->_getAd($cid,2);
        }
        $url = $this->_hashTao();
        foreach ($types as $key=>$value) {
            $type_data[$key]['name'] = $value['name'];
            $type_data[$key]['img'] = Common::getAttachPath() . $value['img'];
            $type_data[$key]['link'] = $url.'&keyword='.urlencode($value['name']);
            if(!empty($value['keyword'])){
                $type_data[$key]['link'] = $url.'&keyword='.urlencode($value['keyword']);
            }
        }

        $this->output(0, '', array('type_data'=>$type_data, 'ad_data'=>$ad_data, 'version'=>$server_version));
	}


    /**
     * @param string $action action 控制输出地址
     * @return array
     */
    private function _getTop($action){
        //获取主分类
        list($total, $pType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0),array('sort'=>'DESC','id'=>'DESC'));
        $pType = Common::resetKey($pType, 'id');
        //获取主分类 获取子分类
        list($total, $cType)  = Type_Service_Ptype::getsBy(array('pid'=>array('IN',array_keys($pType))));

        $static_root = Common::getAttachPath() ;
        $web_root = Common::getWebRoot();
        $cType = Common::resetKey($cType,'pid');
        $data = array();
        foreach ($pType as $key=>$value) {
            if(empty($cType[$value['id']])) continue;
            $data[] = array(
              'id' => $value['id'],
              'name' => html_entity_decode($value['name']),
              'icon' => $static_root . $value['icon'],
              'link' =>sprintf('%s/api/apk_type/%s?id=%d',$web_root,$action,$value['id'])
            );
        }
        return $data;
    }

    /**
     * 获取子类和区分推荐
     * @param $cid 一级分类ID
     * @return array
     */
    private function _getData($cid){
        $page = 1;
        $per = 50;
        //获取顶级分类
        $big = Type_Service_Ptype::getBy(array('id'=>$cid));
        if(empty($big)){
            //获取顶级分类的第一条
            list(,$list) = Type_Service_Ptype::getList(array('pid'=>0,'status'=>1));
            $big = $list[0];
        }
        //获取顶级分类的所有子类
        list(,$cType) = Type_Service_Ptype::getsBy(array('pid'=>$big['id'],'status'=>1),array('sort'=>'DESC','id'=>'DESC'));
        //获取有效的二级分类
        if(!empty($cType)){
            $cType_id = array_keys(Common::resetKey($cType, 'id'));
            list($total, $types) = Type_Service_Type::getsBy(array('ctype_id' => array('IN', $cType_id), 'type_id' => $cid, 'status' => 1), array('sort' => 'DESC', 'id' => 'DESC'));
        }else{
            list($total, $types) = Type_Service_Type::getsBy(array('type_id' => $cid, 'status' => 1), array('sort' => 'DESC', 'id' => 'DESC', 'status' => 1));
        }
        //hash
        $url = $this->_hashTao();
        $rec = $child = array();
        $webroot = Common::getWebRoot();

        foreach ($types as $key=>$value) {
            $child[$value['ctype_id']][$key]['name'] = $value['name'];
            $child[$value['ctype_id']][$key]['img'] = Common::getAttachPath() . $value['img'];
            $child[$value['ctype_id']][$key]['link'] = $url.'&keyword='.urlencode($value['name']);
            if(!empty($value['keyword'])){
                $child[$value['ctype_id']][$key]['link'] = $url.'&keyword='.urlencode($value['keyword']);
            }
            if($value['is_recommend']){
                $rec[$key]['name'] = $value['name'];
                $rec[$key]['img'] = Common::getAttachPath() . $value['img'];
                $rec[$key]['link'] = $url.'&keyword='.urlencode($value['name']);
                if(!empty($value['keyword'])){
                    $rec[$key]['link'] = $url.'&keyword='.urlencode($value['keyword']);
                }
            }
        }
        $recommend = array();
        if($big['recommend_status']&&$big['recommend']&&!empty($rec)){
            $recommend = array('name'=>$big['recommend'],'items'=>array_values($rec));
        }

        $child_type_data = array();
        if(empty($cType)){
            $child_type_data=array(
                'name'=>'默认',
                'items'=>array_values($child[0])
            );
            return array($child_type_data,$recommend);
        }
        foreach ($cType as $k => $v) {
            if(empty($child[$v['id']]))continue ;
            $child_type_data[] = array(
              'name' => html_entity_decode($v['name']),
              'items' => array_values($child[$v['id']])
            );
        }
        return array($child_type_data,$recommend);
    }

    private function _hashTao(){
        $type_taobao_search = json_decode(Gou_Service_Config::getValue('type_taobao_search'), true);
        return $url = Common::tjurl(
          Stat_Service_Log::URL_SEARCH,
          Stat_Service_Log::V_APK,
          $type_taobao_search['module_id'],
          $type_taobao_search['channel_id'],
          0,
          $type_taobao_search['url'],
          'apk分类搜索搜索',
          $type_taobao_search['channel_code']
        );
    }

    /**
     * 获取广告
     * @param int $cid 大分类id
     * @param int $channel 版本
     * @return array
     */
    private function _getAd($cid,$channel=1){
        $ad_params = array('status'=>1, 'start_time'=>array('<', Common::getTime()),'end_time'=>array('>', Common::getTime()),'ad_type'=>6, 'channel_id'=>$channel);
        if($cid) {
            $ad_params['Ptype_id']=$cid;
            //update 统计
            Type_Service_Ptype::updateTJ($cid);
        } else {
            $ad_params['is_recommend']=1;
        }

        $ad = Gou_Service_Ad::getBy($ad_params, array('sort' => 'DESC', 'id' => 'DESC'));
        if (!$ad)  return array();
        $ad_data = array(
          'name' => $ad['title'],
          'img' => Common::getAttachPath() . $ad['img'],
          'link' => Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $ad)
        );
        return $ad_data;
    }
}

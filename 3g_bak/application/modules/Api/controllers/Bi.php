<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 *  *BI统计接口
 *  * @author tiger
 *
 */
class BIController extends Api_BaseController {

	public static $tmp= array();
    public $modules = array('banner广告模块', '搜索模块', '热门站点模块', '分类栏目模块', '其它', '网址大全', '本地化导航', '书签');


    //导航页关键字
    /**
     * module = 模块
     * col_name = 子模块
     * type_name = 分类
     */
    public function urlAction() {
        $tStr = $this->getInput('t');
        $tArr = explode(',', $tStr);
        $rc   = Common::getCache(1);
        $list = array();
        foreach ($tArr as $t) {
            $rcKey   = 'ToUrl:' . $t;
            $tmpInfo = $rc->get($rcKey);
            $info    = json_decode($tmpInfo, true);
            $title   = $column_name = $type_name = $module = $label_id_list = '';
            $rcKey1  = 'TMP_SHORT_URL_INFO:' . $t;
            $tInfo   = $rc->get($rcKey1);
            if (empty($tInfo)) {
                if ($info['type'] == 'NAV') {
                    $content       = Gionee_Service_Ng::get($info['id']);
                    $title         = $content['title'];
                    $column        = Gionee_Service_NgColumn::get($content['column_id']);
                    $column_name   = $column['name'];
                    $type          = Gionee_Service_NgType::get($column['type_id']);
                    $type_name     = $type['name'];
                    $label_id_list = $content['label_id_list'];
                    //获得模块名
                    if ($type['id'] == '1' && $column['style'] == 'img1') {
                        $module = $this->modules[0];
                    } elseif ($type['id'] == '1' && $column['style'] == 'hot_nav') {
                        $module = $this->modules[2];
                    } elseif (in_array($type['id'], array(2, 3, 4, 5, 6, 22, 23, 24, 25, 26, 27))) {
                        $module = $this->modules[3];
                    } else {
                        $module = $this->modules[4];
                    }
                } else if ($info['type'] == 'SOHU') {
                    $content     = Gionee_Service_Sohu::getAd($info['id']);
                    $title       = $content['title'];
                    $module      = Gionee_Service_Sohu::$positions[$content['position']];
                    $column_name = '';
                    if (isset(Gionee_Service_Sohu::$BiColumnName[$content['position']][$content['sort']])) {
                        $column_name = Gionee_Service_Sohu::$BiColumnName[$content['position']][$info['sort']];
                    }
                    $type_name = '';
                } else if ($info['type'] == 'INBUILT') {
                    $content     = Gionee_Service_Inbuilt::get($info['id']);
                    $title       = $content['name'];
                    $column_name = $content['cate'];
                } elseif ($info['type'] == 'SITE') {
                    $content     = Gionee_Service_SiteContent::get($info['id']);
                    $title       = $content['name'];
                    $module      = $this->modules[5];
                    $category    = Gionee_Service_SiteCategory::get($content['cat_id']);
                    $column_name = $category['name'];
                    if (isset($category['parent_id'])) {
                        $typeMsg   = Gionee_Service_SiteCategory::getBy(array('id' => $category['parent_id']));
                        $type_name = $typeMsg['name'];
                    }
                } else if ($info['type'] == 'LOCAL_NAV') {
                    $content       = Gionee_Service_LocalNavList::get($info['id']);
                    $title         = $content['name'];
                    $column        = Gionee_Service_LocalNavColumn::get($content['column_id']);
                    $column_name   = $column['name'];
                    $type          = Gionee_Service_LocalNavType::get($column['type_id']);
                    $type_name     = $type['name'];
                    $module        = $this->modules[6];
                    $label_id_list = $content['label_id_list'];
                } else if ($info['type'] == 'BAIDU_HOT') {
                    $queryUrl = parse_url(urldecode(html_entity_decode($info['_url'])), PHP_URL_QUERY);
                    parse_str($queryUrl, $queryOut);
                    $title = urldecode($queryOut['word']);
                }

                if (Common::isOverseas() && $info['type'] == 'NAV') {
                    $info['type'] = 'LOCAL_NAV';
                }

                $tInfo = array(
                    't'         => $t,
                    'id'        => $info['id'],
                    'type'      => $info['type'],
                    'title'     => $title,
                    'col_name'  => $column_name,
                    'type_name' => $type_name,
                    'module'    => $module,
                    'label'     => $label_id_list,
                    'url'       => html_entity_decode($info['_url']),
                );
                $rc->set($rcKey1, $tInfo, Common::T_ONE_DAY);
            }

            $list[] = $tInfo;
        }

        echo Common::jsonEncode($list);
        exit;
    }

    public function labelDataAction() {
        $dataList = Gionee_Service_Label::getAll(array('level' => 'asc')); 
     	$ret =  $this->_getAllParentsData($dataList);
     	echo Common::jsonEncode($ret);
        exit();
    }

    private function _getAllParentsData($dataList){
    	$data = array();
    	$obj = array();
    	foreach ($dataList as $node){
    			$obj[$node['id']] = $node;
    	}
    	foreach ($dataList as $m=>$n){
    		$tmp = array();
    		array_push($tmp, $n['id'],$n['name']);
    		$preId = $n['parent_id'];
    	 	while(intval($preId)){
    			array_push($tmp,$obj[$preId]['id'],$obj[$preId]['name']); //上级内容
    			$preId = $obj[$preId]['parent_id'];
    		}
    		$data[]  = $tmp; 
    	}
  		unset($obj);
  		return $data;
    }
    
   private function _allPreLevelData($id){
   		$arr = array();
   		$ret = Gionee_Service_Label::get($id);
   		array_push(self::$tmp, $ret['id'],$ret['name'],$ret['level']);
   		   if(intval($ret['parent_id'])){
   			  $this->_allPreLevelData($ret['parent_id']);
   			}
      }
   
      private function _tree($arr,$id){
      	static $data = array();
      	foreach ($arr as $m){
      		if($m['parent_id'] == $id){
      			$data[]  = $m;
      			if($m['parent_id']>0){
      				$this->_tree($arr, $m['parent_id']);
      			}
      		}
      	}
      	return $data;
      }
    
    private function _export($data, $title) {
        ini_set('memory_limit', '1024M');
        header('Content-Type: application/vnd.ms-excel;charset=GB2312');
        $filename = $title . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
        $out = fopen('php://output', 'w');
        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($out, array('ID', '三级标签名', '二级标签ID', '二级标签名', '一级标签ID', '一级标签名'));
        foreach ($data as $k => $v) {
            fputcsv($out, array(
                $v['id'],
                $v['name'],
                $v['parent_id'],
                $v['parent_name'],
                $v['grand_id'],
                $v['grand_name']
            ));
        }
        fclose($out);
    }
}
<?php
/**
 * 广告位模型
 *
 * @author Intril.Leng
 */

Doo::loadController("AppDooController");
class AdPositionController extends AppDooController {

    /**
     * AdPoses模型对象
     * @var Object
     */
    private $_adPositionModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_adPositionModel = Doo::loadModel('AdPosition', TRUE);
    }

    /**
     * 显示APP列表，查询结果显示
     */
    public function index() {
        # START 检查权限
        if (!$this->checkPermission(ADPOS, ADPOS_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $whereArr = array('parent_id' => 0);
        $url = "/AdPosition/index?";
        // 分页
        $total = $this->_adPositionModel->records($whereArr);
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->_adPositionModel->findAll($whereArr);
        $allParent = $this->_adPositionModel->findAll("parent_id != 0");
        $all = $this->_adPositionModel->findAll(array('parent_id' => 0));
        $listAll = listArray($all, 'pos_key', 'pos_name');
        $parentArr = array();
        if (!empty($allParent)){
            foreach($allParent as $key => $val){
                $parentArr[$val['parent_id']] .= $listAll[$val['pos_key']]."<br />";
            }
        }
        $this->data['parentArr'] = $parentArr;

        // 选择模板
        $this->myrender('adposition/list', $this->data);
    }

    /**
     * 编辑/添加
     */
    public function edit() {
        # START 检查权限
        if (!$this->checkPermission(ADPOS, ADPOS_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        if (isset($get['id']) || $get['id']){// 修改
            $this->data['title'] = '编辑';
            $this->data['result'] = $this->_adPositionModel->findOne(array('id' => $get['id']));
            $parentIds = $this->_adPositionModel->findAll(array('parent_id' => $get['id']));
            $allList = $this->_adPositionModel->findAll(array("parent_id" => 0));
            $listParent = listArray($parentIds, 'pos_key', 'id');
            $this->data['parentIdsArr'] = array();
            foreach($allList as $val){
                if (array_key_exists($val['pos_key'], $listParent)){
                    $this->data['parentIdsArr'][$val['id']] = $val['pos_name'];
                }
            }
            $this->data['leftSel'] = array_diff(listArray($allList, 'id', 'pos_name'), $this->data['parentIdsArr']);
        }  else {
            $this->data['title'] = '添加';
            $allList = $this->_adPositionModel->findAll(array("parent_id" => 0));
            $this->data['leftSel'] = listArray($allList, 'id', 'pos_name');
            $this->data['parentIdsArr'] = array();
            $this->data['result'] = array(
                'id' => '', 'parent_id' => '', 'pos_key' => '', 'pos_name' => '', 'ad_type' => '', 'ad_subtype' => ''
            );
        }
        // 选择模板
        $this->myrender('adposition/detail', $this->data);
    }

    /**
     * 保存
     */
    public function save () {
        # START 检查权限
        if (!$this->checkPermission(ADPOS, ADPOS_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if (empty($post['pos_key'])){
            $this->redirect("javascript:history.go(-1)","请填写广告类型ID");
        }
        if (empty($post['pos_name'])){
            $this->redirect("javascript:history.go(-1)","请填写广告类型名字");
        }
        if (!$post['id']){ //新建
            $checkPosKey = $this->_adPositionModel->records(array('pos_key' => $post['pos_key'],'parent_id' => 0));
            if ($checkPosKey > 0){
                $this->redirect("javascript:history.go(-1)","广告类型ID已存在，请用其它广告类型ID");
            }
            $checkPosName = $this->_adPositionModel->records("pos_name ='".$post['pos_name']."' AND parent_id = 0");
            if ($checkPosName > 0){
                $this->redirect("javascript:history.go(-1)","广告类型名字已存在，请用其它广告类型名字");
            }
        }
        $allAdPoses = $this->_adPositionModel->findAll(array('parent_id' => '0'));
        $post['parent_id'] = 0;
        $id = $this->_adPositionModel->upd($post['id'], $post);
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_1');
        // 删除Redis
        $redis->delete($post['pos_key'] . "_pos");
        $child = $this->_adPositionModel->findAll(array('parent_id' => $id));
        // 删除原有的子元素redis
        foreach($child as $citem){
            $redis->delete($citem['pos_key'] . "_pos");
        }
        // 删掉所有当前的支持广告类型
        $this->_adPositionModel->del(array('parent_id' => $id));
        // redis 里面对应删除所有pos_key
        $listAdPoses = listArray($allAdPoses, 'id', 'pos_key');
        if (isset($post['parent_ids']) && !empty($post['parent_ids'])){
            $insertData = array(
                'parent_id' => $id,
                'pos_key' => '',
                'pos_name' => '',
                'ad_type' => '',
                'ad_subtype' => ''
            );
            foreach ($post['parent_ids'] as $parentId){
                $insertData['pos_key'] = $listAdPoses[$parentId];
                // 删除新关联的子元素Redis
                $redis->delete($insertData['pos_key'] . "_pos");
                $this->_adPositionModel->upd(NULL, $insertData);
            }
        }
        if (!$post['id']){
            $self = $this->_adPositionModel->findOne(array('id' => $id));
            // 添加时，默认关联自己
            $this->_adPositionModel->upd(NULL, array('parent_id' => $id,  'pos_key' => $self['pos_key'],'pos_name' => '', 'ad_type' => '', 'ad_subtype' => ''));
        }
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '自定义广告类型', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $id);
        $this->redirect('/AdPosition/index');
    }

    /**
     * 删除应用
     */
    public function delete() {
        # START 检查权限
        if (!$this->checkPermission(ADPOS, ADPOS_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $id = $this->get['id'];
        if (!$id){
            $this->redirect("/AdPosition/index", "无效Id参数，删除失败");
        }
        $rs = $this->_adPositionModel->findOne(array("id" => $id));
        // 检查应用能否被删除掉（被产品使用的应用和正在使用的中，都不能删除）
        $adInfoModel = Doo::loadModel('datamodel/AdInfo', TRUE);
        $count = $adInfoModel->count(array("where" => "pos = '".$rs['pos_key']."'", 'asArray' => TRUE, 'select' => '*'));
        if ($count > 0){
            $this->redirect("/AdPosition/index", "该配置已被使用中，请先取消此配置的使用，再删除");
        }
        // 删除Redis
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_1');
        $redis->delete($rs['pos_key'] . "_pos");
        // 删除当前下面所有子元素的Redis
        $child = $this->_adPositionModel->findAll(array('parent_id' => $id));
        foreach($child as $citem){
            $redis->delete($citem['pos_key'] . "_pos");
        }
        $this->_adPositionModel->del(array("id" => $id));
        $this->_adPositionModel->del(array('parent_id' => $id));
        $this->userLogs(array('msg' => json_encode(array('id' => $id)), 'title' => '自定义广告类型', 'action' => 'delete'));
        $this->redirect('/AdPosition/index',"删除成功");
    }

    public function getpos(){
        $get = $this->get;
        $result = $this->_adPositionModel->findAll(array("ad_type" => $get['type'], 'ad_subtype' => $get['subtype'], 'parent_id' => 0));
        if (empty($result)){
            echo json_encode(array('msg' => -1, 'result' => ''));
        }else{
            echo json_encode(array('msg' => 1, 'result' => $result));
        }
    }
    public function setstate(){
        $get = $this->get;
        $result=$this->_adPositionModel->setPosState($get["pos_key"],$get["state"]);
        if($result){
            echo json_encode(array('msg' => 0, 'result' =>'设置成功'));
        }else{
            echo json_encode(array('msg' => -1, 'result' =>'设置失败,请重新设置'));
        }
    }
}
<?php

/**
 * 角色管理
 *
 * @author Intril.leng
 */
Doo::loadController("AppDooController");

class RoleController extends AppDooController {

    /**
     * Role模型对象
     * @var Object
     */
    private $_roleModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_roleModel = Doo::loadModel('Role', TRUE);
    }

    public function index() {
        # START 检查权限
        if (!$this->checkPermission(ROLE, ROLE_VIEW)) {
            $this->displayNoPermission();
        }
        if (!$this->checkPermission(ROLE, ROLE_VIEW)) {
            define('CAN_ROLE_REMOVE_ROLE', 0);
        } else {
            define('CAN_ROLE_REMOVE_ROLE', 1);
        }
        $params = $this->get;
        if (isset($params['product_name']) && $params['product_name'] != 0) {
            $whereArr['product_name'] = $params['product_name'];
            $url .= "product_name=".$params['product_name']."&";
        }else{
            //$params['product_name'] = 0;
        }
        if (isset($params['role_id']) && $params['role_id'] != 0) {
            $whereArr['role_id'] = $params['role_id'];
            $url .= "role_id=".$params['role_id']."&";
        }else{
            $params['role_id'] = 0;
        }
        // 分页
        $url = "/role/index?";
        $total = $this->_roleModel->recordsr2p($params);
        $total=$total["num"];
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->_roleModel->view(array("role_id"=>$params["role_id"],"product_name"=>$params["product_name"]));
        $roleModel = Doo::loadModel('Role', TRUE);
        $roleInfo = $roleModel->findAll();
        $this->data['role'] = listArray($roleInfo, 'id', 'title', array('0' => '所有角色'));
        $this->data['params'] = $params;
        $this->data['select'] = form_select($this->data['role'], array('name' => 'role_id', 'value' => $this->data['params']['role_id']));
        
        $this->myrender("role/list", $this->data);
    }

    public function edit() {
        # START 检查权限
        if (!$this->checkPermission(ROLE, ROLE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        // 数据效验

        if (isset($get['id']) && $get['id'] && is_numeric($get['id'])) {// 编辑
            $whereArr = array('id' => $get['id']);
            $this->data['result'] = $this->_roleModel->findOne($whereArr);
            if (!$this->data['result']) {
                $this->redirect("javascript:history.go(-1)","该用户组不存在或已被删除");
            }
            $GLOBALS['role_id'] = $get['id']; // 为下面select控件用
            $this->data['title'] = '修改';
            $role_id = $get['id'];
        }else{
            $this->data['result'] = array('id' => '', 'title' => '');
            $this->data['title'] = '添加';
        }
        $result = array();
        foreach (Doo::conf()->PERMISSIONS as $groupId => $v) {
            $pGroupOption = '';
            $result[$groupId]['title'] = Doo::conf()->P_GROUP[$groupId];
            $size = count($v);
            $pGroupOption = "<select name=\"p_group[$groupId][]\"  size=\"$size\" multiple style=\"width: 300px;height:auto;\">";
            foreach ($v AS $permissionId => $desc) {
                if ($this->checkPermission($groupId, $permissionId)) {
                    $selected = " selected ";
                } else {
                    $selected = "";
                }
                $pGroupOption .= "<option value='" . $permissionId . "' $selected> $desc </option>\n";
            }
            $pGroupOption .= "</select>";
            $result[$groupId]['option'] = $pGroupOption;
        }
        $sql="SELECT a.product_id as id,b.`product_name`, b.platform FROM mobgi_backend.roles2products a LEFT JOIN mobgi_api.`ad_product_info` b ON a.`product_id`=b.`id` where a.role_id='".$get["id"]."'";
        $right=Doo::db()->query($sql)->fetchAll();
//        $sql="SELECT b.id,b.`product_name` FROM mobgi_api.`ad_product_info` b LEFT JOIN mobgi_backend.roles2products a ON a.`product_id`!=b.`id` WHERE a.`product_id`!=b.`id` AND b.role_id='".$this->data['session']["role_id"]."'";
//        echo $sql;
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $left=$product->find(array("select"=>'id,product_name,platform','asArray' => TRUE));
        foreach($left as $k=>$pr){
            if(in_array($pr,$right)){
                unset($left[$k]);
            }
        }
        $left=  json_encode($left);
        $right=  json_encode($right);
        $this->data["right"]=$right;
        $this->data["left"]=$left;
        
        $this->data['select'] = $result;
        $this->myrender("role/detail", $this->data);
    }

    /**
     * 保存角色
     */
    public function save() {
        # START 检查权限
        if (!$this->checkPermission(ROLE, ROLE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $_POST;
        if (empty($post['title'])){
            $this->redirect("javascript:history.go(-1)","用户组名不能为空");
        }
        // 添加到role表
        $insertId = $this->_roleModel->upd($post['id'], array('title' => $post['title']));
        $roles2PermissionsModel = Doo::loadModel('Roles2permissions', TRUE);
        if (!empty($insertId)){
            $roles2PermissionsModel->del($insertId);
        }
        if ($post['p_group']){
            Doo::loadClass("permission/class.permission");
            $permiss = new permission();
            foreach ($post['p_group'] AS $groupId => $permission) {
                $accessmask = $permiss->create_accessmask($groupId, $permission);
                $roles2PermissionsModel->upd(array('role_id' => $insertId, 'group_id' => $groupId, 'mask' => $accessmask));
            }
        }
        $role2products=Doo::loadModel("datamodel/Roles2products",TRUE);
        $role2products->roleUpdate($post["product_id"],$insertId);
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '角色列表', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $post['id']);
        $this->redirect("../role/index");
    }

    public function delete(){
        # START 检查权限
        if (!$this->checkPermission(ROLE, ROLE_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限

        $id = $this->get['id'];
        if (empty($id)){
            $this->redirect("javascript:history.go(-1)","没有ID值");
        }
        // 查询角色下面的用户
        $adminModel = Doo::loadModel('Admin', TRUE);
        $isHasUser = $adminModel->findOne(array('role_id' => $id));
        if ($isHasUser){
            $this->redirect("/role/index", "删除失败，角色下面有用户");
        }
        $this->_roleModel->del($id);
        $roles2PermissionsModel = Doo::loadModel('Roles2permissions', TRUE);
        $roles2PermissionsModel->del($id);
        $this->userLogs(array('msg' => json_encode($this->get), 'title' => '角色列表', 'action' => 'delete'));
        $this->redirect("/role/index");
    }
}
?>

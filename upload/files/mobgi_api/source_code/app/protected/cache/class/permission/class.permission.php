<?php

class permission {

    var $PERMISSIONS;
    var $P_GROUP;
    var $session;

    function permission() {
        $this->PERMISSIONS = Doo::conf()->PERMISSIONS;
        $this->P_GROUP = Doo::conf()->P_GROUP;
        $this->session = Doo::session()->__get("admininfo");
    }

    function create_accessmask($group_id, $permission) {
        $permission = array_flip($permission);
        $accessmask = str_repeat("0", count($permission));
        while (list($k, $v) = each($this->PERMISSIONS[$group_id])) {
            $flag = (isset($permission[$k]) ? "1" : "0");
            $accessmask[$k] = $flag;
        }
        return $accessmask;
    }

    function check_permission($group_id, $permission_id) {
        global $role_id;
        $accessmask = $this->get_accesssmask($role_id, $group_id);
        return (bool) $accessmask[$permission_id];
    }

    // 限于检查当前登录用户是否具有某个权限组的任一权限
    function check_grouppermission($group_id) {
        if (!$group_id) {
            return false;
        }
        $role_id = $this->session['role_id'];
        $rs = Doo::db()->query("SELECT * FROM mobgi_backend.roles2permission WHERE role_id='" . $role_id . "' and group_id='" . $group_id . "'");
        $result = array();
        while ($row = $rs->fetch()){
            $result[] = $row;
        }
        return $result;
    }

    // 获取某个角色对应某个组的所有权限
    function get_accesssmask($role_id, $group_id) {
        if (!$role_id) {
            $role_id = $this->session['role_id'];
        }
        $rs = Doo::db()->query("SELECT * FROM mobgi_backend.roles2permission WHERE role_id='" . $role_id . "' and group_id='" . $group_id . "'");
        $result = $rs->fetch();
        if (!empty($result)) {
            return $result['mask'];
        }
        return false;
    }

}

?>

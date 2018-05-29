<?php

if (!defined('BASE_PATH')) exit('Access Denied!');
//菜单配置
$config['Admin_System'] = array(
    'name' => '系统',
    'sid' => 'sys',
    'items' => array(
        'Admin_User',
        'Admin_Group',
        'Admin_Passwd'
    ),
);

$config['Admin_File'] = array(
    'name' => '主题',
    'sid' => 'zt',
    'items' => array(
        'Admin_File',
        'Admin_Filetype',
        'Admin_themeup',
        'Admin_Themenew',
        'Admin_Editersort',
        'Admin_Themenewspc',
        'Admin_Fileadmin',
        'Admin_Subject',
        'Admin_Upnew',
        'Admin_Searchwords',
        'Admin_Ucenter',
        'Admin_Msg',
        'Admin_Themehot',
    )
);

$config['Admin_wallpaper'] = array(
    'name' => '壁纸',
    'sid' => 'bz',
    'items' => array(
        'Admin_Wallpaper',
        'Admin_Wallpapermyupload',
        'Admin_Wallpapermy',
        'Admin_Wallpaperadminsubject',
        'Admin_Wallpapersetnew',
        'Admin_Wallpaperadmin',
        'Admin_Hotwallpaper',
    )
);

$config['Admin_wallpaperlive'] = array(
    'name' => '动态壁纸',
    'sid' => 'lbz',
    'items' => array(
        'Admin_Wallpaperlive',
        'Admin_Wallpaperadminlive',
        'Admin_Livewallpapersubject',
        'Admin_Wallpaperadminengin',
    //'Admin_Hot',
    )
);

$config['Admin_clock'] = array(
    'name' => '时钟',
    'sid' => 'sz',
    'items' => array(
        'Admin_Clockmyupload',
        'Admin_Clockmy',
        'Admin_Clocksubject',
        'Admin_Clockres',
    )
);

$config['Admin_pay'] = array(
    'name' => '支付',
    'sid' => 'pay',
    'items' => array(
        'Admin_Payindex',
        'Admin_Paylist',
        'Admin_Payapply',
        'Admin_Paymember',
        'Admin_Paymsg',
        'Admin_Paydesigner',
        //'Admin_Paydesignerinfo',
        //'Admin_Paycheck',
    )
);

$config['Admin_other'] = array(
    'name' => '其它',
    'sid' => 'other',
    'items' => array(
        'Admin_Startpage',
    )
);

$entry = Yaf_Registry::get('config')->adminroot;
$view = array(
    /*     * ************************ 系统管理 ************************ */
    'Admin_User' => array('用户管理', $entry . '/Admin/User/index', 'myThemes'),
    'Admin_Group' => array('用户组管理', $entry . '/Admin/Group/index', 'myThemes'),
    'Admin_Passwd' => array('修改密码', $entry . '/Admin/Passwd/index', 'myThemes'),
    /*     * ************************ 系统管理 ************************ */

    /*     * ************************ 主题*********************** */
    'Admin_File' => array('我的主题', $entry . '/Admin/File/index', 'myThemes'),
    "Admin_themeup" => array("主题上传", $entry . '/Admin/File/add', 'uploadThemes'),
    "Admin_Fileadmin" => array('资源管理', $entry . '/Admin/Fileadmin/index', 'resourceAdmin'),
    'Admin_Filetype' => array('标签管理', $entry . '/Admin/Filetype/index', 'zTtagAdmin'),
    "Admin_Themenew" => array('新品推荐', $entry . '/Admin/Themenew/index', 'newArrivals'),
    "Admin_Editersort" => array('小编推荐', $entry . '/Admin/Themenew/editersort', 'editerArrivals'),
    "Admin_Themenewspc" => array('精品推荐', $entry . '/Admin/Themenew/spcSort', 'spcArrivals'),
    'Admin_Themehot' => array('热门主题', $entry . '/Admin/Themenew/hot', 'themehot'),
    'Admin_Subject' => array('专题管理', $entry . '/Admin/Subject/index', 'special'),
    'Admin_Searchwords' => array('搜索热词', $entry . '/Admin/Searchwords/index', 'searchwords'),
    'Admin_Upnew' => array("主题上新", $entry . '/Admin/upnew/index'),
    /*     * ************************ 主题*********************** */

    /*     * ************************ 壁纸*********************** */
    'Admin_Wallpaper' => array("标签管理", $entry . '/Admin/Wallpaperadmin/typelist', 'tagAdmin'),
    'Admin_Wallpaperadminsubject' => array("专题管理", $entry . '/Admin/Wallpaperadminsubject/index', 'seminar'),
    'Admin_Wallpapersetnew' => array("套图管理", $entry . '/Admin/Wallpaperadmin/adminset', 'taotu'),
    'Admin_Wallpapermy' => array("我的壁纸", $entry . '/Admin/Wallpapermy/index', 'ibz'),
    'Admin_Wallpapermyupload' => array("壁纸上传", $entry . '/Admin/Wallpapermyupload/index', 'bzUpload'),
    "Admin_Wallpaperadmin" => array("资源管理", $entry . '/Admin/Wallpaperadmin/index', 'zy'),
    'Admin_Hotwallpaper' => array("热门壁纸", $entry . '/Admin/Wallpaperadmin/hot', 'hot'),
    /*     * ************************ 壁纸*********************** */


    /*     * ************************ 动态壁纸*********************** */
    'Admin_Wallpaperlive' => array("动态壁纸上传", $entry . '/Admin/Wallpapermy/liveupload', 'liveUpload'),
    'Admin_Wallpaperadminlive' => array("动态壁纸管理", $entry . '/Admin/Wallpaperadmin/livewallpaper', 'live'),
    'Admin_Livewallpapersubject' => array("动态壁纸专题", $entry . '/Admin/Livewallpapersubject/index', 'bzlwpsubject'),
    'Admin_Wallpaperadminengin' => array("动态壁纸引擎", $entry . '/Admin/Wallpaperadminengin/index', 'bzengin'),
    'Admin_Hot' => array("热门动态壁纸", $entry . '/Admin/Wallpaperlive/hot', 'hot'),
    /*     * ************************ 动态壁纸*********************** */


    /*     * ************************ 时钟*********************** */
    'Admin_Clockmyupload' => array("上传时钟", $entry . '/Admin/Clockmy/upload', 'upload'),
    'Admin_Clockmy' => array("我的时钟", $entry . '/Admin/Clockmy/index', 'isz'),
    'Admin_Clocksubject' => array("时钟专题", $entry . '/Admin/Clocksubject/index', 'szsubject_szsubjectAdd'),
    'Admin_Clockres' => array("资源管理", $entry . '/Admin/Clockres/index', 'resource'),
    /*     * ************************ 时钟*********************** */

    /*     * ************************ 支付*********************** */
    'Admin_Payindex' => array("概况", $entry . '/Admin/Pay/index', 'payindex'),
    'Admin_Paylist' => array("明细", $entry . '/Admin/Pay/list', 'paylist'),
    'Admin_Payapply' => array("提现", $entry . '/Admin/Pay/apply', 'payapply'),
    'Admin_Paymember' => array("用户", $entry . '/Admin/Pay/member', 'paymember'),
    'Admin_Paymsg' => array("消息", $entry . '/Admin/Paymsg/apply', 'paymsg'),
    'Admin_Paydesigner' => array("设计师", $entry . '/Admin/Pay/designer', 'paydesigner'),
    //'Admin_Paydesignerinfo' => array("设计师信息", $entry . '/Admin/Paycheck/personallist', 'paydesignerinfo'),
    //'Admin_Paycheck' => array("设师提现", $entry . '/Admin/Paycheck/personalapply', 'paycheck'),
    /*     * ************************ 支付*********************** */

    /*     * ************************ 其它*********************** */
    'Admin_Startpage' => array("开机启动图片", $entry . '/Admin/Client/startpage', 'client'),
    'Admin_Ucenter' => array("用户中心", $entry . '/Admin/Ucenter/edit', 'ucenter', true),
    'Admin_Msg' => array("用户消息", $entry . '/Admin/Ucenter/Msg', 'msg', true),
        /*         * ************************ 其它*********************** */
);

return array($config, $view);

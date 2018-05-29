<?php

$PERMISSIONS = array();
$P_GROUP = array();

/* ----------------------------------开发者管理------------------------------------ */
define('DEVELOP', 1);
define('DEVELOP_LIST', 0);
define('DEVELOP_EDIT', 1);
define('DEVELOP_DEL', 2);
define('DEVELOP_APP', 3);
define('DEVELOP_CHECK', 4);
$PERMISSIONS[DEVELOP][DEVELOP_LIST] = '开发者列表';
$PERMISSIONS[DEVELOP][DEVELOP_EDIT] = '修改/添加开发者';
$PERMISSIONS[DEVELOP][DEVELOP_DEL] = '删除开发者';
$PERMISSIONS[DEVELOP][DEVELOP_APP] = '开发者应用';
$PERMISSIONS[DEVELOP][DEVELOP_CHECK] = '开发者审核';

$P_GROUP[DEVELOP] = '开发者管理';

/* ----------------------------------开发者管理------------------------------------ */
define('APP', 2);
define('APP_LIST', 0);
define('APP_EDIT', 1);
define('APP_DEL', 2);
define('APP_CHECK', 3);
$PERMISSIONS[APP][APP_LIST] = '应用列表';
$PERMISSIONS[APP][APP_EDIT] = '添加/编辑应用';
$PERMISSIONS[APP][APP_DEL] = '删除应用';
$PERMISSIONS[APP][APP_CHECK] = '应用审核';

$P_GROUP[APP] = '应用管理';

/* ----------------------------------广告产品------------------------------------ */

define('PRODUCT', 3);
define('PRODUCT_LIST', 0);
define('PRODUCT_EDIT', 1);
define('PRODUCT_DEL', 2);
define('PRODUCT_MANGER_EDIT', 3);

//$PERMISSIONS[PRODUCT][PRODUCT_LIST] = '产品列表';
$PERMISSIONS[PRODUCT][PRODUCT_EDIT] = '添加/修改产品';
$PERMISSIONS[PRODUCT][PRODUCT_DEL] = '删除产品';
$PERMISSIONS[PRODUCT][PRODUCT_MANGER_EDIT] = '产品名称';

$P_GROUP[PRODUCT] = '广告产品';

define('ADCONFIG', 4);
define('ADCONFIG_LIST', 0);
define('ADCONFIG_EDIT', 1);
define('ADCONFIG_DEL', 2);

$PERMISSIONS[ADCONFIG][ADCONFIG_LIST] = '配制项列表';
$PERMISSIONS[ADCONFIG][ADCONFIG_EDIT] = '添加/修改配制项';
$PERMISSIONS[ADCONFIG][ADCONFIG_DEL] = '删掉配制项';

$P_GROUP[ADCONFIG] = '配制项列表';

define('ADDCONFIG', 5);
define('ADDCONFIG_EDIT', 0);
define('ADDCONFIG_INSTALL_REMINED', 1);
define('ADDCONFIG_PUSH', 2);
$PERMISSIONS[ADDCONFIG][ADDCONFIG_EDIT] = '新增列表广告配制项';
$PERMISSIONS[ADDCONFIG][ADDCONFIG_PUSH] = '新增PUSH广告配制项';
$PERMISSIONS[ADDCONFIG][ADDCONFIG_INSTALL_REMINED] = '安装提醒';

$P_GROUP[ADDCONFIG] = '新增列表广告配制项';
/* ----------------------------------其他配置------------------------------------ */
define('CONDITION', 6);
define('CONDITION_LIST', 0);
define('CONDITION_EDIT', 1);
define('CONDITION_DEL', 2);
$PERMISSIONS[CONDITION][CONDITION_LIST] = "条件管理";
$PERMISSIONS[CONDITION][CONDITION_EDIT] = "添加/修改条件";
$PERMISSIONS[CONDITION][CONDITION_DEL] = "删除条件";

$P_GROUP[CONDITION] = '条件管理';

define('ADPOS', 9);
define('ADPOS_LIST', 0);
define('ADPOS_EDIT', 1);
define('ADPOS_DEL', 2);
$PERMISSIONS[ADPOS][ADPOS_LIST] = "自定义广告类型列表";
$PERMISSIONS[ADPOS][ADPOS_EDIT] = "添加/修改自定义广告类型";
$PERMISSIONS[ADPOS][ADPOS_DEL] = "删除自定义广告类型";

$P_GROUP[ADPOS] = '自定义广告类型管理';


define('ADMIN_ACCOUNT', 7);
define('ADMIN_ACCOUNT_LIST', 0);
define('ADMIN_ACCOUNT_EDIT', 1);
define('ADMIN_ACCOUNT_DEL', 2);
define('ADMIN_ACCOUNT_EDIT_PASS', 3);

$PERMISSIONS[ADMIN_ACCOUNT][ADMIN_ACCOUNT_LIST] = '后台账号管理';
$PERMISSIONS[ADMIN_ACCOUNT][ADMIN_ACCOUNT_EDIT] = "添加/修改帐号";
$PERMISSIONS[ADMIN_ACCOUNT][ADMIN_ACCOUNT_DEL] = '删除帐号';
$PERMISSIONS[ADMIN_ACCOUNT][ADMIN_ACCOUNT_EDIT_PASS] = '修改密码';

$P_GROUP[ADMIN_ACCOUNT] = '后台帐号';


/* ----------------------------------用户管理------------------------------------ */
define('ROLE', 8);
define('ROLE_VIEW', 0);
define('ROLE_EDIT', 1);
define('ROLE_DEL', 2);

$PERMISSIONS[ROLE][ROLE_VIEW] = '角色列表';
$PERMISSIONS[ROLE][ROLE_EDIT] = '添加/编辑角色';
$PERMISSIONS[ROLE][ROLE_DEL] = '删除角色';

$P_GROUP[ROLE] = '角色管理';



$PERMISS_CONFIG['PERMISSIONS'] = $PERMISSIONS;
$PERMISS_CONFIG['P_GROUP'] = $P_GROUP;
/* ----------------------------------CACHE管理------------------------------------ */
define('CACHE', 11);
define('CACHE_EDIT', 0);
$PERMISSIONS[CACHE][CACHE_EDIT] = 'CACHE管理';

$P_GROUP[CACHE] = 'CACHE管理';

/* ----------------------------------资讯管理------------------------------------ */
define('ARTICLE', 13);
define('ARTICLE_LIST', 0);
define('ARTICLE_EDIT',1);
define('ARTICLE_DEL',2);
$PERMISSIONS[ARTICLE][ARTICLE_EDIT] = '添加/编辑资讯';
$PERMISSIONS[ARTICLE][ARTICLE_LIST] = '资讯列表';
$PERMISSIONS[ARTICLE][ARTICLE_DEL] = '删除资讯';

$P_GROUP[ARTICLE] = '资讯管理';

/* ----------------------------------作弊管理------------------------------------ */
define('CHEAT', 14);
define('CHEAT_APP', 0);
define('CHEAT_USER',1);
define('CHEAT_LOG',2);
define('CHEAT_CONFIG',3);
$PERMISSIONS[CHEAT][CHEAT_APP] = 'APP黑名单';
$PERMISSIONS[CHEAT][CHEAT_USER] = '用户黑名单';
$PERMISSIONS[CHEAT][CHEAT_LOG] = '作弊查看日志';
$PERMISSIONS[CHEAT][CHEAT_CONFIG] = '作弊配置设置';

$P_GROUP[CHEAT] = '作弊管理';

define('OTHERCONFIG', 12);
define('OTHERCONFIG_EDIT', 0);
define('OTHERCONFIG_MONITOR', 1);
$PERMISSIONS[OTHERCONFIG][OTHERCONFIG_EDIT] = "其他配置项";
$PERMISSIONS[OTHERCONFIG][OTHERCONFIG_MONITOR] = "监控配置";

$P_GROUP[OTHERCONFIG] = '其他配置项管理';


define('RESOURCE', 15);
define('RESOURCE_VIEW', 0);
define('RESOURCE_EDIT', 1);
define('RESOURCE_CHECK', 3);

$PERMISSIONS[RESOURCE][RESOURCE_VIEW] = "素材查看";
$PERMISSIONS[RESOURCE][RESOURCE_EDIT] = "编辑素材";
$PERMISSIONS[RESOURCE][RESOURCE_CHECK] = "素材审核";

$P_GROUP[RESOURCE] = '产品素材管理';

define('WEB', 10);
define('WEB_MSG_VIEW', 0);
define('WEB_MSG_SEND', 1);

$PERMISSIONS[WEB][WEB_MSG_VIEW] = "查看站内信";
$PERMISSIONS[WEB][WEB_MSG_SEND] = "发送站内信";

$P_GROUP[WEB] = '站内信';

/* ----------------------------------RTB配置------------------------------------ */
define('RTB', 16);
define('RTB_BLACKLIST_VIEW', 0);
define('RTB_BLACKLIST_EDIT', 1);
define('RTB_CONFIG_VIEW', 2);
define('RTB_CONFIG_EDIT', 3);
define('RTB_PLAN_VIEW', 4);
define('RTB_PLAN_EDIT', 5);
define('RTB_WEIGHT_VIEW', 6);
define('RTB_WEIGHT_EDIT', 7);

$PERMISSIONS[RTB][RTB_BLACKLIST_VIEW] = "查看黑名单";
$PERMISSIONS[RTB][RTB_BLACKLIST_EDIT] = "编辑黑名单";
$PERMISSIONS[RTB][RTB_CONFIG_VIEW] = "配置列表";
$PERMISSIONS[RTB][RTB_CONFIG_EDIT] = "编辑配置";
$PERMISSIONS[RTB][RTB_PLAN_VIEW] = "导量计划列表";
$PERMISSIONS[RTB][RTB_PLAN_EDIT] = "导量计划编辑";

$P_GROUP[RTB] = 'RTB管理';

/* ----------------------------------PUSH配置------------------------------------ */
define('PUSH', 17);
//define('PUSH_BLACKLIST_VIEW', 0);
//define('PUSH_BLACKLIST_EDIT', 1);
define('PUSH_CONFIG_VIEW', 2);
define('PUSH_CONFIG_EDIT', 3);
define('PUSH_PLAN_VIEW', 4);
define('PUSH_PLAN_EDIT', 5);
define('PUSH_WEIGHT_VIEW', 6);
define('PUSH_WEIGHT_EDIT', 7);
define('PUSH_HARASS_VIEW', 8);
define('PUSH_HARASS_EDIT', 9);
define('PUSH_LOG_VIEW', 10);

//$PERMISSIONS[PUSH][PUSH_BLACKLIST_VIEW] = "查看黑名单";
//$PERMISSIONS[PUSH][PUSH_BLACKLIST_EDIT] = "编辑黑名单";
$PERMISSIONS[PUSH][PUSH_CONFIG_VIEW] = "配置列表";
$PERMISSIONS[PUSH][PUSH_CONFIG_EDIT] = "编辑配置";
$PERMISSIONS[PUSH][PUSH_PLAN_VIEW] = "导量计划列表";
$PERMISSIONS[PUSH][PUSH_PLAN_EDIT] = "导量计划编辑";
$PERMISSIONS[PUSH][PUSH_WEIGHT_VIEW] = "导量权重列表";
$PERMISSIONS[PUSH][PUSH_WEIGHT_EDIT] = "导量权重编辑";
$PERMISSIONS[PUSH][PUSH_HARASS_VIEW] = "防骚扰列表";
$PERMISSIONS[PUSH][PUSH_HARASS_EDIT] = "防骚扰编辑";
$PERMISSIONS[PUSH][PUSH_LOG_VIEW] = "PUSH日志";

$P_GROUP[PUSH] = 'PUSH管理';

/* ----------------------------------客户管理------------------------------------ */
define('CUSTOMER', 18);
define('CUSTOMER_VIEW', 0);
define('CUSTOMER_EDIT', 1);
define('CUSTOMER_PRODUCT_VIEW', 2);
//define('PUSH_PLAN_EDIT', 5);
//define('PUSH_WEIGHT_VIEW', 6);
//define('PUSH_WEIGHT_EDIT', 7);
//define('PUSH_HARASS_VIEW', 8);
//define('PUSH_HARASS_EDIT', 9);
//define('PUSH_LOG_VIEW', 10);

$PERMISSIONS[CUSTOMER][CUSTOMER_VIEW] = "客户列表";
$PERMISSIONS[CUSTOMER][CUSTOMER_EDIT] = "编辑客户";
$PERMISSIONS[CUSTOMER][CUSTOMER_PRODUCT_VIEW] = "产品名称列表";
//$PERMISSIONS[CUSTOMER][PUSH_PLAN_VIEW] = "导量计划列表";
//$PERMISSIONS[CUSTOMER][PUSH_PLAN_EDIT] = "导量计划编辑";
//$PERMISSIONS[CUSTOMER][PUSH_WEIGHT_VIEW] = "导量权重列表";
//$PERMISSIONS[CUSTOMER][PUSH_WEIGHT_EDIT] = "导量权重编辑";
//$PERMISSIONS[CUSTOMER][PUSH_HARASS_VIEW] = "防骚扰列表";
//$PERMISSIONS[CUSTOMER][PUSH_HARASS_EDIT] = "防骚扰编辑";
//$PERMISSIONS[CUSTOMER][PUSH_LOG_VIEW] = "PUSH日志";

$P_GROUP[CUSTOMER] = '客户管理';


/* ----------------------------------客户管理------------------------------------ */
define('IMPLANTABLE', 19);
define('IMPLANTABLE_APP_VIEW', 0);
define('IMPLANTABLE_APP_EDIT', 1);
define('IMPLANTABLE_PUBLICSH_VIEW', 2);
define('IMPLANTABLE_PUBLICSH_EDIT', 3);
define('IMPLANTABLE_PRODUCT_VIEW', 4);
define('IMPLANTABLE_PRODUCT_EDIT', 5);
define('IMPLANTABLE_BLACKLIST_VIEW', 6);
define('IMPLANTABLE_BLACKLIST_EDIT', 7);
define('IMPLANTABLE_CONFIG_VIEW', 8);
define('IMPLANTABLE_CONFIG_EDIT', 9);
define('IMPLANTABLE_PLAN_VIEW', 10);
define('IMPLANTABLE_PLAN_EDIT', 11);

$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_APP_VIEW] = "应用列表";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_APP_EDIT] = "编辑应用";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_PUBLICSH_VIEW] = "客户列表";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_PUBLICSH_EDIT] = "编辑客户";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_PRODUCT_VIEW] = "产品列表";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_PRODUCT_EDIT] = "编辑产品";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_BLACKLIST_VIEW] = "渠道限制列表";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_BLACKLIST_EDIT] = "编辑渠道限制";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_CONFIG_VIEW] = "配置项列表";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_CONFIG_EDIT] = "编辑渠道限制";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_PLAN_VIEW] = "推广计划列表";
$PERMISSIONS[IMPLANTABLE][IMPLANTABLE_PLAN_EDIT] = "编辑推广计划";

$P_GROUP[IMPLANTABLE] = '植入式广告';

/* ----------------------------------------视频广告商------------------------------*/
define('VIDEOADSCOM', 20);
define('VIDEOADSCOM_VIEW', 0);
define('VIDEOADSCOM_EDIT', 1);
$PERMISSIONS[VIDEOADSCOM][VIDEOADSCOM_VIEW] = "广告商配置";
$PERMISSIONS[VIDEOADSCOM][VIDEOADSCOM_EDIT] = "新增广告商配置";
$P_GROUP[VIDEOADSCOM] = '视频广告商';

/* ----------------------------------------视频聚合配置------------------------------*/
define('VIDEOADS', 21);
define('VIDEOADS_VIEW', 0);
define('VIDEOADS_EDIT', 1);
$PERMISSIONS[VIDEOADS][VIDEOADS_VIEW] = "视频聚合配置";
$PERMISSIONS[VIDEOADS][VIDEOADS_EDIT] = "新增视频聚合配置";
$P_GROUP[VIDEOADS] = '视频聚合列表';



/* ----------------------------------------聚合广告配置------------------------------*/
define('POLYMERICAD', 22);
define('POLYMERICAD_VIEW', 0);
define('POLYMERICAD_EDIT', 1);
$PERMISSIONS[POLYMERICAD][POLYMERICAD_VIEW] = "聚合广告配置";
$PERMISSIONS[POLYMERICAD][POLYMERICAD_EDIT] = "新增聚合广告配置";
$P_GROUP[POLYMERICAD] = '聚合广告配置';

/* ----------------------------------------视频广告配置------------------------------*/
define('INCENTIVEVIDEOADCONF', 23);
define('INCENTIVEVIDEOADCONF_VIEW', 0);
define('INCENTIVEVIDEOADCONF_EDIT', 1);
$PERMISSIONS[INCENTIVEVIDEOADCONF][INCENTIVEVIDEOADCONF_VIEW] = "视频广告配置";
$PERMISSIONS[INCENTIVEVIDEOADCONF][INCENTIVEVIDEOADCONF_EDIT] = "新增视频广告配置";
$P_GROUP[POLYMERICAD] = '视频广告配置';

/* ----------------------------------------应用策略管理------------------------------*/
define('INCENTIVEVIDEOADLIMIT', 24);
define('INCENTIVEVIDEOADLIMIT_VIEW', 0);
define('INCENTIVEVIDEOADLIMIT_EDIT', 1);
$PERMISSIONS[INCENTIVEVIDEOADLIMIT][INCENTIVEVIDEOADLIMIT_VIEW] = "应用策略管理";
$PERMISSIONS[INCENTIVEVIDEOADLIMIT][INCENTIVEVIDEOADLIMIT_EDIT] = "新增应用策略管理";
$P_GROUP[POLYMERICAD] = '应用策略管理';




$PERMISS_CONFIG['PERMISSIONS'] = $PERMISSIONS;
$PERMISS_CONFIG['P_GROUP'] = $P_GROUP;



?>
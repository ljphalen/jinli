<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 支付模板
 */
class PayController extends Admin_BaseController {

    public function indexAction() {
        //设计师角色只能看到自己的统计数据
        $groupId = $this->userInfo['groupid'];
        $pri_where = '';
        if ($groupId == 1) {
            $uid = $this->userInfo['uid'];
            $pri_where = " and uid = $uid";
        }
        //所有销售量及营业额
        $sql = "select count(*) as count, sum(total) as total from pay_order where status = 5 $pri_where";
        $result = Db_Adapter_Pdo::fetch($sql);
        $this->assign('all_num', $result['count']);
        $this->assign('all_coin', $result['total']);
        //Common::v($result);
        //营业额
        $sql = "select sum(income) as sum from pay_apply where status = 2 $pri_where";
        $result = Db_Adapter_Pdo::fetch($sql);
        $this->assign('all_total', $result['sum']);

        $recently = $this->getInput('recently');
        $from = $this->getInput('from');
        $to = $this->getInput('to');
        $where = '';

        //默认为一周前数据
        if (empty($from) && empty($to) && empty($recently) && !isset($_GET['keyword'])) {
            $recently = 'week';
            $from_time = strtotime('-1 week');
        } else {
            $from_time = strtotime('2015/05/02');
        }
        $current_time = time();
        $to_time = $current_time;

        //默认为一周
        if (!empty($recently)) {
            switch ($recently) {
                case 'week':
                    $from_time = strtotime('-1 week');
                    $page = 7;
                    break;
                case 'month':
                    $from_time = strtotime('-1 month');
                    $page = 10;
                    break;
                case 'half_year':
                    $from_time = strtotime('-6 month');
                    $page = 6;
                    break;
                case 'year':
                    $from_time = strtotime('-1 year');
                    $page = 12;
                    break;
                default:
                    $from_time = 0;
                    break;
            }
            $where .= ' and created_time>=' . $from_time;
        }

        if (!empty($from)) {
            $from_time = strtotime($from);
            $where .= ' and created_time>=' . $from_time;
        }
        if (!empty($to)) {
            $to_time = strtotime($to);
            $where .= ' and created_time<=' . $to_time;

            //判断时间段
            if ($to_time == $current_time) {
                if ($from_time == strtotime('-1 week')) {
                    $recently = 'week';
                } elseif ($from_time == strtotime('-1 month')) {
                    $recently = 'month';
                } elseif ($from_time == strtotime('-6 month')) {
                    $recently = 'half_year';
                } elseif ($from_time == strtotime('-1 year')) {
                    $recently = 'year';
                }
            }
        }

        //时间段内销售总量和总营业额
        $sql = "select count(*) as count, sum(total) as total from pay_order where status = 5 $where $pri_where";

        $result = Db_Adapter_Pdo::fetch($sql);
        $this->assign('recently_num', $result['count']);
        $this->assign('recently_coin', $result['total']);
        $count_num = $result['count'];

        //统计时间段从 $from_time 到 $to_time
        $jump = ($to_time - $from_time) / $page;
        $jump = round($jump);
        $keys = array();
        for ($i = 0; $i < $page; $i++) {
            $keys[$i] = date('Y-m-d', $from_time + $jump / 2 + $jump * $i);
        }
        $sorts = array();
        //数量
        $sql = "select created_time from pay_order where status = 5 $where $pri_where";
        $result = Db_Adapter_Pdo::fetchAll($sql);

        //初始化
        foreach ($keys as $k) {
            $sorts[$k] = 0;
        }
        //Common::v($keys);
        foreach ($result as $r) {
            $ctime = $r['created_time'];
            for ($i = 0; $i < $page; $i++) {
                if ($from_time + $jump / 2 + $jump * $i <= $ctime && $ctime <= $from_time + $jump / 2 + $jump * ($i + 1)) {
                    $sorts[$keys[$i]] += 1;
                }
            }
        }

        ksort($sorts);
        //Common::v($sorts);
        $this->assign('keys', $keys);
        $this->assign('from', date('Y-m-d H:i:s', $from_time));
        $this->assign('to', date('Y-m-d H:i:s', $to_time));
        $this->assign('recently', $recently);
        $this->assign('sorts', array_values($sorts));
        $this->assign("meunOn", "pay_payindex_index");
    }

    //过滤公共部分
    private function filter() {
        $keyword = $this->getInput('keyword');
        $recently = $this->getInput('recently');
        $from = $this->getInput('from');
        $to = $this->getInput('to');

        $where = 'where 1 = 1 ';

        //默认为一周前数据
        if (empty($from) && empty($to) && empty($recently) && !isset($_GET['keyword'])) {
            $recently = 'week';
            $from_time = strtotime('-1 week');
        } else {
            $from_time = strtotime('2015/05/02');
        }
        $current_time = time();
        $to_time = $current_time;

        //默认为一周
        if (!empty($recently)) {
            switch ($recently) {
                case 'week':
                    $from_time = strtotime('-1 week');
                    break;
                case 'month':
                    $from_time = strtotime('-1 month');
                    break;
                case 'half_year':
                    $from_time = strtotime('-6 month');
                    break;
                case 'year':
                    $from_time = strtotime('-1 year');
                    break;
                default:
                    $from_time = 0;
                    break;
            }
            $where .= ' and a.created_time>=' . $from_time;
        }

        if (!empty($from)) {
            $from_time = strtotime($from);
            $where .= ' and a.created_time>=' . $from_time;
        }
        if (!empty($to)) {
            $to_time = strtotime($to);
            $where .= ' and a.created_time<=' . $to_time;

            //判断时间段
            if ($to_time == $current_time) {
                if ($from_time == strtotime('-1 week')) {
                    $recently = 'week';
                } elseif ($from_time == strtotime('-1 month')) {
                    $recently = 'month';
                } elseif ($from_time == strtotime('-6 month')) {
                    $recently = 'half_year';
                } elseif ($from_time == strtotime('-1 year')) {
                    $recently = 'year';
                }
            }
        }

        $this->assign('from', date('Y-m-d H:i:s', $from_time));
        $this->assign('to', date('Y-m-d H:i:s', $to_time));
        $this->assign('recently', $recently);
        $this->assign('keyword', $keyword);

        return $where;
    }

    public function listAction() {
        //设计师角色只能看到自己的统计数据
        $groupId = $this->userInfo['groupid'];
        $pri_where = '';
        if ($groupId == 1) {
            $uid = $this->userInfo['uid'];
            $pri_where = " and a.uid = $uid";
        }

        $limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;
        $offset = ($page - 1) * $limit;

        //区分所有和个人
        $type = $this->getInput('type') ? : 'all';
        $sort = $this->getInput('sort');
        $sort_type = $this->getInput('sort_type') ? : 'desc';

        $where = $this->filter();
        $keyword = $this->getInput('keyword');
        if (!empty($keyword)) {
            $where .= " and a.product_name like '%$keyword%'";
        }
        //sort
        if (!empty($sort)) {
            switch ($sort) {
                case 'price':
                    $sort = 'a.price';
                    break;
                case 'down':
                    $sort = 'all_download_times';
                    break;
                case 'purchase_times':
                    $sort = 'all_purchase_times';
                    break;
                case 'total':
                    $sort = 'all_total';
                    break;
                default:
                    $sort = 'a.price';
                    break;
            }
            $sort = " order by $sort $sort_type";
        }

        $from_field = "from pay_order as a
		left join pay_price_change_log as b on a.price_id = b.id
		$where
		and a.status = 5 $pri_where";

        $sql = "select count(distinct a.price_id) as item_num, count(*) as all_purchase_times, sum(a.total) as all_total, a.order_id,
		a.product_id, a.product_name, a.price,
		(select img from theme_file_img where file_id=a.product_id limit 1) as img,
		sum(b.download_times) as all_download_times
		$from_field
		group by a.product_id
		$sort
		limit $offset,$limit";
        //echo $sql;exit;

        $themes = Db_Adapter_Pdo::fetchAll($sql);
        //Common::v($themes);

        foreach ($themes as $key => $theme) {
            $themes[$key]['cover'] = substr($theme['img'], 0, strrpos($theme['img'], '/')) . '/pre_face.jpg';

            //item_num 大于1的比较少
            if ($theme['item_num'] > 1) {
                $sql = "select count(*) as purchase_times, sum(a.total) as total, a.order_id,
					a.product_id, a.product_name, a.price, sum(b.download_times) as download_times
					from pay_price_change_log as b
					left join pay_order as a on b.id = a.price_id
					$where
					and a.product_id = $theme[product_id]
					group by b.id";
                //echo $sql; exit;
                $items = Db_Adapter_Pdo::fetchAll($sql);
                $themes[$key]['items'] = $items;
            }
        }

        $count_sql = "select count(distinct a.product_id) as count $from_field";
        $count_result = Db_Adapter_Pdo::fetch($count_sql);
        $this->showPages($count_result['count'], $page, $limit, $pages);

        $this->assign('themes', $themes);
        $this->assign("meunOn", "pay_paylist_list");
    }

    public function detailAction() {
        $id = $this->getInput('themeId');
        $theme = Theme_Service_File::get($id);


        $imgs = Theme_Service_FileImg::getsBy(array('file_id' => $id));
        foreach ($imgs as $k => $img) {
            $imgs[$k] = str_replace('.webp', '.jpg', $img);
        }

        //一级标签
        $themetype = Theme_Service_IdxFileType::getBy(array('file_id' => $id));
        $filetype = Theme_Service_FileType::getAll();
        foreach ($filetype as $type) {
            if ($themetype['type_id'] == $type['id']) {
                $theme['type_name'] = $type['name'];
                break;
            }
        }

        //二级标签
        $theme_sec_cate = Theme_Service_IdxFilesedType::getsBy(array('file_id' => $id));
        if (!empty($theme_sec_cate)) {
            $all_sec_cate = Theme_Service_FilesedType::getAll();
            foreach ($all_sec_cate as $type) {
                foreach ($theme_sec_cate as $s_type) {
                    if ($s_type['sedtype_id'] == $type['id']) {
                        $theme['sec_type_name'][] = $type['name'];
                        break;
                    }
                }
            }
        }

        //状态
        $status = array(
            1 => '已提交',
            2 => '未通过',
            3 => '已通过',
            4 => '上架',
            5 => '下架',
        );
        $this->assign('status', $status);

        //购买次数
        //$purchase_times = Theme_Service_Order::count(array('product_id'=> $id));
        //$theme['purchase_times'] = $purchase_times;

        $sql = "select distinct ucenter_id, b.user_rname as nick_name
	   	from pay_order as a
	   	left join theme_user_center as b on a.ucenter_id = b.user_sys_id
	   	where a.product_id = $id";
        $buyers = Theme_Service_Order::fetchAll($sql);
        $this->assign('buyers', $buyers);
        //Common::v($buyers);
        //下载次数
        $sql = "select sum(download_times) as download_times from pay_price_change_log where product_id = $id";
        $download_result = Theme_Service_Order::query($sql);
        $theme['download_times'] = $download_result['download_times'];

        //购买次数及营业额
        $sql = "select sum(total) as sum, count(*) as count from pay_order where product_id = $id";
        $order_result = Theme_Service_Order::query($sql);
        $theme['purchase_times'] = $order_result['count'];
        $theme['sum'] = $order_result['sum'];
        //var_dump($theme);exit;
        //Common::v($imgs);
        $this->assign('theme', $theme);
        $this->assign('imgs', $imgs);
        $this->assign("meunOn", "pay_paylist_detail");
    }

    public function memberAction() {
        $limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;
        $offset = ($page - 1) * $limit;

        $sort = $this->getInput('sort');
        $sort_type = $this->getInput('sort_type') ? : 'desc';

        $where = $this->filter();
        $keyword = $this->getInput('keyword');
        if (!empty($keyword)) {
            $where .= " and a.product_name like '%$keyword%'";
        }

        //sort
        if (!empty($sort)) {
            switch ($sort) {
                case 'down':
                    $sort = 'all_download_times';
                    break;
                case 'purchase_times':
                    $sort = 'all_purchase_times';
                    break;
                case 'total':
                    $sort = 'all_total';
                    break;
                default:
                    $sort = 'all_purchase_times';
                    break;
            }
            $sort = " order by $sort $sort_type";
        }


        $from_field = "from pay_order as a
		left join pay_price_change_log as b on a.price_id = b.id
		left join theme_user_center as c on a.ucenter_id = c.user_sys_id
		$where
		and a.status = 5
		group by a.ucenter_id ";

        $sql = "select  c.user_id, c.user_rname as nick_name, sum(b.download_times) as all_download_times,
		count(*) as all_purchase_times, sum(a.total) as all_total
		$from_field
		$sort
		limit $offset, $limit";

        $members = Db_Adapter_Pdo::fetchAll($sql);
        $this->assign('members', $members);

        $count_sql = "select count(*) as count $from_field";
        $count_result = Db_Adapter_Pdo::fetch($count_sql);
        $this->showPages($count_result['count'], $page, $limit, $pages);
        //Common::v($designers);

        $this->assign("meunOn", "pay_paymember_member");
    }

    public function memberinfoAction() {
        $memberId = $this->getInput('memberId');
        $member = Theme_Service_Usercenter::get($memberId);
        $ucenter_id = $member['user_sys_id'];

        $this->assign('member', $member);
        //Common::v($memberInfo);
        //购买个数
        $sql = "select  a.created_time, c.apply_price, b.title
		from pay_order as a
		left join theme_file as b on a.product_id = b.id
		left join pay_price_change_log as c on a.price_id = c.id
		where a.status = 5 and a.ucenter_id = '$ucenter_id'";
        $products = Db_Adapter_Pdo::fetchAll($sql);
        $this->assign('products', $products);

        $this->assign("meunOn", "pay_paymember_info");
    }

    public function designerAction() {
        $limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;
        $outExcl = $this->getInput("outExcl")? : false;
        $offset = ($page - 1) * $limit;

        $sort = $this->getInput('sort');
        $sort_type = $this->getInput('sort_type') ? : 'desc';

        $where = $this->filter();
        $keyword = $this->getInput('keyword');
        if (!empty($keyword)) {
            $where .= " and a.product_name like '%$keyword%'";
        }

        //sort
        if (!empty($sort)) {
            switch ($sort) {
                case 'down':
                    $sort = 'all_download_times';
                    break;
                case 'total':
                    $sort = 'all_total';
                    break;
                case 'count':
                    $sort = 'all_count';
                    break;
                case 'sum':
                    $sort = 'all_sum';
                    break;
                case 'income':
                    $sort = 'all_income';
                    break;
                default:
                    $sort = 'all_download_times';
                    break;
            }
            $sort = " order by $sort $sort_type";
        }

        $from_field = "from pay_order as a
		left join pay_price_change_log as b on a.price_id = b.id
		left join admin_user as c on a.uid = c.uid
		left join theme_file as d on a.product_id = d.id
		$where
		and a.status = 5
		group by a.uid ";

        $sql = "select  a.uid, c.nick_name, count(d.id) as all_total, count(b.download_times) as all_download_times,
		count(distinct a.product_id) as all_count, sum(a.total) as all_sum,
		(select sum(income) from pay_apply as e where e.uid = a.uid) as all_income
		$from_field
		$sort
		limit $offset, $limit";

        $designers = Db_Adapter_Pdo::fetchAll($sql);
        if ($outExcl) {
            Theme_Service_PhpToExcl::OutMessage_excl("psheji", $designers);
            exit;
        }

        $this->assign('designers', $designers);

        $count_sql = "select count(*) as count $from_field";
        $count_result = Db_Adapter_Pdo::fetch($count_sql);
        $this->showPages($count_result['count'], $page, $limit, $pages);
        //Common::v($designers);

        $this->assign("meunOn", "pay_paydesigner_designer");
    }

    public function designerdetailAction() {
        $uid = $this->getInput('uid');

        $designer = Admin_Service_User::getBy(array('uid' => $uid));
        $data = array(
            'user_id' => $uid,
        );
        $themes = Theme_Service_File::getsBy($data);
        //echo count($themes);exit;
        $sql = "select a.price, a.create_time, a.title,
		(select b.img from theme_file_img as b  where b.file_id = a.id limit 1) as img
		from theme_file as a
		where a.user_id = $uid ";
        $themes = Db_Adapter_Pdo::fetchAll($sql);
        foreach ($themes as $key => $theme) {
            $themes[$key]['cover'] = substr($theme['img'], 0, strrpos($theme['img'], '/')) . '/pre_face.jpg';
        }

        //Common::v($themes);
        $this->assign('themes', $themes);

        //总下载次数
        $sql = "select sum(download_times) as download_times from pay_price_change_log where uid = $uid";

        $download_result = Db_Adapter_Pdo::fetch($sql);
        $designer['download_times'] = $download_result['download_times'];
        //总营业额
        $sql = "select sum(total) as sum from pay_order where status = 5 and uid = $uid";
        $sum_result = Db_Adapter_Pdo::fetch($sql);
        $designer['sum'] = $sum_result['sum'];
        //Common::v($designer);

        $this->assign('designer', $designer);
        //Common::v($themes);
        $this->assign("meunOn", "pay_paydesigner_designerdetail");
    }

    public function applyAction() {
        //时间为本月以前的所有未提现的自然月
        $apply_time = strtotime(date('Y-m'));

        $apply = $this->getApply($apply_time);
        //Common::v($apply);
        $uid = $this->userInfo['uid'];
        $designer = Admin_Service_Userinfo::getBy(array('uid'=>$uid));
        $this->assign('designer', $designer);

        $this->assign('apply', $apply);
        $this->assign('apply_time', $apply_time);
        $this->assign("meunOn", "pay_payapply_apply");

        //Common::v($userDetail);
        if ($designer['designer_type']) {
            $this->display('companyapply');
        } else {
            $this->display('personalapply');
        }
        exit;
    }

    public function do_applyAction() {
        $apply_time = $this->getPost('apply_time');
        $apply = $this->getApply($apply_time);
        if (!$apply['allow']) {
            exit(-1);
        }

        $tax = 0.2;
        $data = array(
            'uid' => $this->userInfo['uid'],
            'nick_name' => $this->userInfo['nick_name'],
            'coin' => $apply['sum'],
            'num' => $apply['count'],
            'total' => $apply['total'],
            'add_value_tax' => $apply['add_value_tax'],
            'channel_cost' => $apply['channel_cost'],
            'income' => $apply['income'],
            'tax' => $apply['tax'],
            'final_income' => $apply['final_income'],
            'sys_income' => $apply['sys_income'],
            'designer_income' => $apply['designer_income'],
            'status' => 0,
            'apply_time' => $apply_time,
            'created_time' => time(),
        );

        Theme_Service_PayApply::insert($data);
        echo 1;
    }

    //获取某一时间点前的提现申请数据
    private function getApply($applytime) {
        $uid = $this->userInfo['uid'];
        $userDetail = Admin_Service_Userinfo::getBy(array('uid' => $uid));
        //a币对人民币暂定为1:1
        $rate = 1;
        $income_rate = 0.7; //3/7分成,设计师7， 平台3
        $sql = "select count(*) as count, sum(price) as sum
		from pay_order
		where created_time < $applytime and status = 5 and pay_off = 0";
        $apply = Db_Adapter_Pdo::fetch($sql);
        $total = $apply['sum'] * $rate;
        $designer_type = $userDetail['designer_type'];
        if ($designer_type) {
            //企业设计师计算方法
            $type = $type = $this->getInput('type');
            $apply = Theme_Service_PayApply::getIncome($total, $designer_type, $type);
        } else {
            //个人设计师计算方法
            $apply = Theme_Service_PayApply::getIncome($total, $designer_type);
        }


        //是否允许提现: 10-15号才允许提交， 单次分成达到100元
        $day = date('d', $applytime);
        $allow = true;
        if (10 <= $day && $day <= 15) {
            $allow = false;
        }
        if ($apply['designer_income'] < 100) {
            $allow = false;
        }
        $apply['allow'] = $allow;

        return $apply;
    }

    public function applylistAction() {
        $limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;
        $offset = ($page - 1) * $limit;
        $params = array('uid' => $this->userInfo['uid']);
        $applys = Theme_Service_PayApply::getList($offset, $limit, $params, array('created_time' => 'desc'));

        $uid = $this->userInfo['uid'];
        $designer = Admin_Service_Userinfo::getBy(array('uid' => $uid));
        $this->assign('designer', $designer);

        $status = array(
            0 => '待付款',
            2 => '已付款',
        );
        $this->assign('status', $status);
        $this->assign('applys', $applys);
        $count = Theme_Service_PayApply::count($params);
        $this->showPages($count, $page, $limit, $pages);
        $this->assign("meunOn", "pay_paymsg_apply");
    }

    public function repaireAction() {
        $themes = Theme_Service_File::getAll();

        foreach ($themes as $theme) {
            if (empty($theme['price_id'])) {
                $data = array(
                    'is_default' => 1,
                    'status' => 1,
                    'uid' => $theme['user_id'],
                    'product_id' => $theme['id'],
                    'created_time' => time(),
                );
                Theme_Service_PriceChangeLog::insert($data);
                $price_id = Theme_Service_PriceChangeLog::getLastInsertId();

                $data = array('price_id' => $price_id);
                Theme_Service_File::update($data, $theme['id']);
            }
        }
        echo 'done';
        exit;
        //Common::v($themes);
    }

}

<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 支付消息
 */
class PaymsgController extends Admin_BaseController {

    /**
     * 审核提现申请
     * @return null
     */
    public function checkapplyAction(){
        $applyId = $this->getInput('applyId');
        $status = $this->getInput("status");
        $reason = $this->getInput('reason');
        $data = array(
            'status'=>$status,
            'reason' => $reason,
        );
        Theme_Service_PayApply::update($data, $applyId);
        echo 1;exit;
    }
    /**
     * 提现申请列表
     * @return null
     */
    public function applyAction() {
        $limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;

        $outExcl = $this->getInput("outExcl")? : false;
        $offset = ($page - 1) * $limit;

        $where = $this->filter();
        $where .= " and (status=0 or status=2)";
        $keyword = $this->getInput('keyword');
        if (!empty($keyword)) {
            $where .= " and nick_name like '%$keyword%'";
        }
        //sort
        $sort_arr = array('created_time' => 'desc');
        $sort = $this->getInput('sort');
        $sort_type = $this->getInput('sort_type') ? : 'desc';
        if (!empty($sort)) {
            switch ($sort) {
                case 'tax':
                    $sort = 'tax';
                    break;
                case 'final_income':
                    $sort = 'final_income';
                    break;
                case 'income':
                    $sort = 'income';
                    break;
                case 'created_time':
                    $sort = 'created_time';
                    break;
                default:
                    $sort = 'created_time';
                    break;
            }
            $sort_arr = array($sort => $sort_type);
        }

        $applys = Theme_Service_PayApply::searchBy($offset, $limit, $where, $sort_arr);

        $status = array(
            0 => '待付款',
            2 => '已付款',
        );

        if ($outExcl) {
            Theme_Service_PhpToExcl::OutMessage_excl("message", $applys);
            exit;
        }

        $this->assign('status', $status);
        $this->assign('applys', $applys);
        $count = Theme_Service_PayApply::searchCount($where);
        $this->showPages($count, $page, $limit, $pages);

        $this->assign("meunOn", "pay_paymsg_paymsgapply");
    }

    //过滤公共部分
    private function filter() {
        $keyword = $this->getInput('keyword');
        $recently = $this->getInput('recently');
        $from = $this->getInput('from');
        $to = $this->getInput('to');

        $where = '1=1';

        //默认为一周前数据
        if (empty($from) && empty($to) && empty($recently) && !isset($_GET['keyword'])) {
            $recently = 'week';
            $from_time = strtotime('-1 week');
        } else {
            $from_time = 0;
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

        $this->assign('from', date('Y-m-d H:i:s', $from_time));
        $this->assign('to', date('Y-m-d H:i:s', $to_time));
        $this->assign('recently', $recently);
        $this->assign('keyword', $keyword);

        return $where;
    }

    /**
     * 价格修改申请列表
     * @return null
     */
    public function changepriceAction() {
        $limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;
        $offset = ($page - 1) * $limit;
        $keyword = $this->getInput("keyword");
        $this->assign("keyword", $keyword);
        $where = " and b.title like '%$keyword%'";

        //sort
        $sort = $this->getInput('sort');
        $sort_type = $this->getInput('sort_type') ? : 'desc';
        if (!empty($sort)) {
            switch ($sort) {
                case 'current_price':
                    $sort = 'a.current_price';
                    break;
                case 'apply_price':
                    $sort = 'a.apply_price';
                    break;
                case 'created_time':
                    $sort = 'a.created_time';
                    break;
                case 'apply_time':
                    $sort = 'a.apply_time';
                    break;
                default:
                    $sort = 'a.created_time';
                    break;
            }
            $sort = " order by $sort $sort_type";
        }

        $sql = "select a.id, d.nick_name, a.status, a.current_price, a.apply_price, a.created_time, a.apply_time, b.title, c.img
		from  pay_price_change_log as a
		left join theme_file as b on a.product_id = b.id
		left join theme_file_img c on b.id = c.file_id 
        left join admin_user d on a.uid = d.uid
		where a.is_default = 0 $where
		group by a.id
		$sort
		limit $offset, $limit";

        $prices = Db_Adapter_Pdo::fetchAll($sql);
        foreach ($prices as $key => $price) {
            $prices[$key]['cover'] = substr($price['img'], 0, strrpos($price['img'], '/')) . '/pre_face.jpg';
        }
        //Common::v($prices);

        $this->assign('prices', $prices);

        $sql = "select count(*) as count
		from  pay_price_change_log as a
		left join theme_file as b on a.product_id = b.id
		where a.is_default = 0 $where
		group by a.id";
        $count = Db_Adapter_Pdo::fetch($sql);
        $this->showPages($count['count'], $page, $limit, $pages);
        $this->assign("meunOn", "pay_paymsg_paymsgchangeprice");
    }

    /**
     * 修改价格详情
     * @return null
     */
    public function cpdetailAction() {
        $status = array(
            '1' => '通过',
            '2' => '不通过',
        );
        $this->assign('status', $status);
        $price_id = $this->getInput('id');
        $price = Theme_Service_PriceChangeLog::get($price_id);
        $this->assign('price', $price);
        //Common::v($price);
        $themeId = $price['product_id'];
        $theme = Theme_Service_File::get($themeId);

        $author = Admin_Service_User::get($theme['user_id']);
        $theme['author'] = $author['nick_name'];
        //Common::v($author);

        $imgs = Theme_Service_FileImg::getsBy(array('file_id' => $themeId));
        foreach ($imgs as $k => $img) {
            $imgs[$k] = str_replace('.webp', '.jpg', $img);
        }
        //Common::v($imgs);
        $this->assign('imgs', $imgs);

        //一级标签
        $themetype = Theme_Service_IdxFileType::getBy(array('file_id' => $themeId));
        $filetype = Theme_Service_FileType::getAll();
        foreach ($filetype as $type) {
            if ($themetype['type_id'] == $type['id']) {
                $theme['type_name'] = $type['name'];
                break;
            }
        }

        //二级标签
        $theme_sec_cate = Theme_Service_IdxFilesedType::getsBy(array('file_id' => $themeId));
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
        $this->assign('theme', $theme);
        //Common::v($theme);

        $this->assign("meunOn", "pay_paymsg_cpdetail");
    }

    public function changestatusAction() {
        $status = $this->getInput('status');
        $id = $this->getInput('id');
        $reason = $this->getInput('reason');

        //获取价格详情
        $price = Theme_Service_PriceChangeLog::get($id);
        $data = array(
            'apply_time' => time(),
            'apply_reason' => $reason,
            'status' => $status,
        );

        $r = Theme_Service_PriceChangeLog::update($data, $id);
        if ($r) {
            //更改产品价格, 如果是通过，应用当前价格，否则需要进行价格回退
            if ($status == 1) {
                $data = array(
                    'price' => $price['apply_price'],
                );
            } elseif ($status == 2) {
                $data = array(
                    'price' => $price['current_price'],
                );
            }
            Theme_Service_File::update($data, $price['product_id']);
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }

    public function checkAction() {

        exit;
    }

    public function testAction() {
        $a = Theme_Service_PhpToExcl::OutMessage_excl();

        exit;
    }

}

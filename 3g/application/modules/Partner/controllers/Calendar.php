<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 合作:日历接口
 *
 */
class CalendarController extends Front_BaseController {

    public function listAction() {
        $date     = $this->getInput('date');
        $t_bi     = $this->getSource();
        $date     = empty($date) ? date('Y-m-d') : $date;
        $md       = date('n-j', strtotime($date));
        $showDate = date('n月j日', strtotime($date));
        $where    = array(
            'md' => $md,
        );
        $list     = Partner_Service_HistoryToday::getDao()->getsBy($where, array('sort_year' => 'desc'));
        $data     = array();
        foreach ($list as $val) {
            $dateArr  = explode('-', $val['date']);
            $titleArr = explode(' ', $val['title']);
            if (count($dateArr) == 4) {
                $val['title'] = '公元前' . $dateArr[1] . '年 ' . $titleArr[1];
            } else if ($dateArr[0] < 1000) {
                $val['title'] = '公元' . $dateArr[0] . '年 ' . $titleArr[1];
            }
            $desc                 = mb_substr($val['desc'], 0, 40, 'utf-8') . (mb_strlen($val['desc'], 'utf-8') > 40 ? '…' : '');
            $data[$val['type']][] = array(
                'title' => $val['title'],
                'type'  => $val['type'],
                //'link'  => $val['link'],
                'link'  => Common::getCurHost() . '/partner/calendar/detail?id=' . $val['id'],
                'desc'  => $desc,
            );
        }

        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PARTNER_PV, '1:calendar_list');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_PARTNER_UV, '1:calendar_list', $t_bi);

        $this->assign('date', $date);
        $this->assign('list1', $data[2]);
        $this->assign('list2', $data[1]);
        $this->assign('showDate', $showDate);
        $banner = Nav_Service_NewsAd::getListByPos('partner_calender_list');
        $this->assign('banner', $banner);
        $adstxt = Nav_Service_NewsAd::getListByPos('partner_calender_list_txt');
        $this->assign('adstxt', $adstxt);
    }

    public function detailAction() {
        $id   = $this->getInput('id');
        $t_bi = $this->getSource();
        $info = Partner_Service_HistoryToday::getDao()->get($id);

        if (empty($info['id'])) {
            exit;
        }
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PARTNER_PV, $info['id'] . ':calendar_detail');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_PARTNER_UV, $info['id'] . ':calendar_detail', $t_bi);
        if (empty($info['content'])) {
            $info['content'] = $info['desc'];
        } else {

            if (stristr($info['content'], '下一页')) {
                $info['content'] = preg_replace('/\s[\d]*下一页/', '', $info['content']);
                Partner_Service_HistoryToday::getDao()->update(array('content' => $info['content']), $info['id']);
            }

            $contents = json_decode($info['content'], true);
            $txt      = '';
            foreach ($contents as $v) {
                if (!empty($v['value'])) {
                    if ($v['type'] == 2) {
                        $txt .= sprintf("<img src=\"%s\">", Common::getImgPath() . $v['value']);
                    } else {
                        $txt .= "<p>{$v['value']}</p>";
                    }
                }
            }


            $info['content'] = $txt;
        }

        $where = array(
            'md'   => $info['md'],
            'type' => $info['type'],
            'id'   => array('>', $id),
        );

        $list     = Partner_Service_HistoryToday::getDao()->getList(0, 3, $where, array('date' => 'desc'));
        $lastList = array();
        foreach ($list as $val) {
            $lastList[] = array(
                'title' => $val['title'],
                'link'  => Common::getCurHost() . '/partner/calendar/detail?id=' . $val['id']
            );
        }

        $this->assign('info', $info);
        $this->assign('lastList', $lastList);
        $banner = Nav_Service_NewsAd::getListByPos('partner_calender_content');
        $this->assign('banner', $banner);
    }


}
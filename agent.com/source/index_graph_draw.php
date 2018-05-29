<?php


require 'plugin/smarty.php';
$toexcel=get('toexcel');
$channeltype  = get('channeltype')?intval(get('channeltype')):1;
$clientid  = intval(get('clientid'));
$subclientid  = intval(get('subclientid'));
$reporttype  = get('reporttype')?get('reporttype'):'day';

$gameid  = intval(get('gameid'));
$keyword  = get('keyword');
$dateStart  = get('dateStart');
$dateEnd  = get('dateEnd');
$selectDate  = get('selectDate');
$detail  = get('detail');

//$pagesize = get('pagesize')?intval(get('pagesize')):15;
$page = get('page')?intval(get('page')):1;
$graph = get('graph')?intval(get('graph')):0;
/* if($toexcel){
	$pagesize = 0;
} */
$pagesize = 0;

if(empty($dateStart) && empty($dateEnd)){
	$dateStart = date('Y-m-d',strtotime('-1 day'));
	$dateEnd = date('Y-m-d',strtotime('-1 day'));
}

//$dateStart = date('2012-12-25');
//$dateEnd = date('2012-12-27');

$dataPrompt="报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
//昨天报表
if($selectDate=="yesterday")
{
	$dateStart  = date("Y-m-d",strtotime('-1 day'));
	$dateEnd  = $dateStart;
	$dataPrompt="昨日报表：&nbsp;&nbsp;".$dateStart;
}
//最近7天报表
if($selectDate=="hebdomad")
{
	$dateStart  = date("Y-m-d",strtotime('-7 day'));
	$dateEnd  = date("Y-m-d",strtotime('-1 day'));
	$dataPrompt="最近7天报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
		
}
//本月报表
if($selectDate=="month")
{
	$dateStart  = date("Y-m-1");
	$dateEnd  = date("Y-m-d",strtotime('-1 day'));
	//$dateStart = '2012-12-01';
	//$dateEnd = '2012-12-31';
	$dataPrompt="本月报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
}

//连接数据库
//require 'sys/libs/db.class.php';
$db = null;
if($detail){
	//连接数据库
	$db = db::factory(get_db_config());
}
//调用数据接口
//require 'sys/libs/report.class.php';
$report = new report($db);

if($keyword!='' && !is_numeric($keyword)){
	//取公司渠道号
	$companys = $report->get_channel_list($channeltype);
	foreach($companys as $k=>$v){
		if(strpos($v['name'],$keyword)!==false){
			$clientid = $v['clientid'];
			break;
		}
	}
}elseif(is_numeric($keyword)){
	$clientid = intval($keyword);
}

$subclientid = -1;
//取登录用户信息
$levels = $sess_userinfo['level']>200?200:$sess_userinfo['level'];
if($levels<200){
	$channeltype = $sess_userinfo['channeltype'];
	$clientid = $sess_userinfo['clientid'];
	$subclientid = $sess_userinfo['clientids'];
}
if(file_exists(GRAPHCONFIG)){
   $tmpConfig = include  GRAPHCONFIG;


    $sac = get('sac');
    $shape = get('shape');
    if($sac == 'report'){
        /* $records = $report->get_report_list($channeltype, $gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
        $totalrow  = $records['rows'];
        $list  = $records['list'];
        $counts  = $records['counts']; */
        if($levels == 30){
        	//汇总
        	//$records = $report->get_report_list($channeltype, $gameid, $clientid, $dateStart, $dateEnd, 1, 1);
        	//$lists  = $records['list'];
        	//$counts  = $records['counts'];
        
        	//子渠道
        	$records = $report->get_report_sub_list($gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
        	$totalrow  = $records['rows'];
        	$list  = $records['list'];
        
        }else{
        	$records = $report->get_report_list($channeltype, $gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
        	$totalrow  = $records['rows'];
        	$list  = $records['list'];
        	$counts  = $records['counts'];
        }
        $photo = array();
        switch($shape){
            case 1:
                $count = 0;
                foreach($list as $key => $var){
                    if($count < $tmpConfig['pie']){
                        $photo[$var['name']] = $var['registerusers'];
                    }else{
                        $photo['其他'] += $var['registerusers'];
                    }
                    $count++;
                }
                if(array_sum($photo) > 0){
                    $graph = new mygraph('pie',$tmpConfig['pie_w'],$tmpConfig['pie_h']);
                    $graph->title = '新增用户注册图';
                    $graph->button_line = true;
                    //$data = array('a'=>20,'b'=>10,'c'=>100);

                    $graph->draw($photo);
                }
            break;
            case 2:
                $count = 0;
                foreach($list as $key => $var){
                    if($count < $tmpConfig['pie']){
                        $photo[$var['name']] = $var['consumemoney'];
                    }else{
                        $photo['其他'] += $var['consumemoney'];
                    }
                    $count++;
                }
                if(array_sum($photo) > 0){
                    $graph = new mygraph('pie',$tmpConfig['pie_w'],$tmpConfig['pie_h']);
                    $graph->title = '用户消费图';
                    $graph->yaxis = '收入';
                    $graph->button_line = true;
                    //$data = array('a'=>20,'b'=>10,'c'=>100);

                    $graph->draw($photo);
                }
            break;
           
        }
    }elseif($sac == 'report_sub'){
        $clientid  = intval(get('clientid'));
        //$subclientid  = intval(get('subclientid'));
        $report = new report();

        //取渠道信息
        $companys = $report->get_channel_list(1)+$report->get_channel_list(2);
        $company_info = $companys[$clientid];
        $channeltype = $company_info['channeltype'];
        $companys = null;

        //汇总
        $records = $report->get_report_list($channeltype, $gameid, $clientid, $dateStart, $dateEnd, 1, 1);
        $totalrow  = $records['rows'];
        $list  = $records['list'];

        //子渠道
        $records = $report->get_report_sub_list($gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
        $totalrow  = $records['rows'];
        $sublist  = $records['list'];
        //var_dump($_GET);
        switch($shape){
             case 1:
                $count = 0;
                foreach($sublist as $key => $var){
                    if($count < $tmpConfig['pie']){
                        $photo[$var['name']] = $var['registerusers'];
                    }else{
                        $photo['其他'] += $var['registerusers'];
                    }
                    $count++;
                }
                //var_dump($photo);
                if(!empty($photo) && array_sum($photo) > 0){
                    $graph = new mygraph('pie',$tmpConfig['pie_w'],$tmpConfig['pie_h']);
                    $graph->title = '新增用户注册图';
                    $graph->button_line = true;
                    //$data = array('a'=>20,'b'=>10,'c'=>100);

                    $graph->draw($photo);
                }
            break;
            case 2:
                $count = 0;
                foreach($sublist as $key => $var){
                    if($count < $tmpConfig['pie']){
                        $photo[$var['name']] = $var['consumemoney'];
                    }else{
                        $photo['其他'] += $var['consumemoney'];
                    }
                    $count++;
                }
                if(array_sum($photo) > 0){
                    $graph = new mygraph('pie',$tmpConfig['pie_w'],$tmpConfig['pie_h']);
                    $graph->title = '用户消费图';
                    $graph->yaxis = '收入';
                    $graph->button_line = true;
                    //$data = array('a'=>20,'b'=>10,'c'=>100);

                    $graph->draw($photo);
                }
            break;
        }
    }else{
        $clientid  = intval(get('clientid'));
        $subclientid  = intval(get('subclientid'));
        $reporttype  = get('reporttype')?get('reporttype'):'day';
        if($levels==10){
            $subclientid = $sess_userinfo['clientids'];
        }
        if($dateStart!=$dateEnd)
        {
                $thedates=$dateStart.'~'.$dateEnd;
        }
        else
        {
                $thedates=$dateStart;
        }
        
        $report = new report();

        if($keyword!='' && !is_numeric($keyword)){

                if($levels>=200){
                        //取公司渠道号
                        $companys = $report->get_channel_list(1)+$report->get_channel_list(2);
                        foreach($companys as $k=>$v){
                                if(strpos($v['name'],$keyword)!==false){
                                        $clientid = $v['clientid'];
                                        break;
                                }
                        }
                }else{
                        //取子渠道号
                        $companys = $report->get_subchannel_list($clientid);
                        foreach($companys as $k=>$v){
                                if(strpos($v['name'],$keyword)!==false){
                                        $subclientid = $v['clientids'];
                                        break;
                                }
                        }
                }
        }elseif(is_numeric($keyword)){
                if($levels>=200){
                        $clientid = intval($keyword);
                }else{
                        $subclientid = intval($keyword);
                }
        }

        //print_r($report->get_cache_filename('2013-3-1', '2013-3-27'));exit;

        if($subclientid==-1){
                $records = $report->get_report_count_list($reporttype, $gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
        }else{
                $records = $report->get_report_sub_count_list($reporttype, $gameid, $clientid, $subclientid, $dateStart, $dateEnd, $pagesize, $page);
        }
        
        $totalrow  = $records['rows'];
        $list  = $records['list'];
        $counts  = $records['counts'];
        //var_dump($list);exit;
        switch($shape){
             case 1:
                $count = 0;
                foreach($list as $key => $var){
                    if($count < $tmpConfig['pie']){
                        $photo[8801][$var['today']] = $var['registerusers'];
                    }
                    $count++;
                }
                //var_dump($photo);exit;
                //if(array_sum($photo) > 0){
                    $graph = new mygraph('bar',$tmpConfig['bar_h'],$tmpConfig['bar_w']);
                    $graph->title = '新增用户注册图';
                    $graph->button_line = true;
                    //$data = array('a'=>20,'b'=>10,'c'=>100);
                    
                    //$graph->draw(array('Java'=>array(1,2,3,4,5)));
                    $graph->draw($photo);
               // }
            break;
            case 2:
                $count = 0;
                foreach($list as $key => $var){
                    if($count < $tmpConfig['bar']){
                        $photo[$var['name']][$var['today']] = $var['consumemoney'];
                    }
                    $count++;
                }
                //if(array_sum($photo) > 0){
                    $graph = new mygraph('bar',$tmpConfig['bar_h'],$tmpConfig['bar_w']);
                    $graph->title = '用户消费图';
                    $graph->yaxis = '收入';
                    $graph->button_line = true;
                    //$data = array('a'=>20,'b'=>10,'c'=>100);

                    $graph->draw($photo);
               // }
            break;
        }
    }
}
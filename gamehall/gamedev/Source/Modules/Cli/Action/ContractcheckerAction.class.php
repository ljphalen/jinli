<?php
/**
 * 检查合同过期工具
 * @author noprom
 *
 */
class ContractcheckerAction extends CliBaseAction
{
	function index()
	{
        $now = time();
        $m = D('Dev://Contract');
        $contracts = $m->where(array('status'=>'4'))->field('id,etime')->select();
        foreach($contracts as $v){
            $delta = $v['etime'] - $now;
            if($delta > 0 && $delta < 2592000){
                $status = 5;
            }elseif ($delta < 0){
                $status = -3;
            }
            $m->where(array('id'=>$v['id']))->setField('status',$status);
        }
	}
}
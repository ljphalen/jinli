<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php,v 1.3 2012/02/20 01:06:48 liushr Exp $

class Page {
    // 分页栏每页显示的页数
    public $rollPage = 6;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow	;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  = array(
    	'redirect'=>false,
    	'header'=>'条记录',
    	'prev'=>'上一页',
    	'next'=>'下一页',
    	'first'=>'第一页',
    	'last'=>'最后一页',
    	'theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows='',$parameter='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        //$_GET验证
        $this->nowPage = intval($_GET[$this->varPage]);
        $this->nowPage = $this->nowPage > 0 ? $this->nowPage : 1;
        
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function show() {
    	
    	if(0 == $this->totalRows) return '';
    	
    	//处理参数
        $p = $this->varPage;
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        
        /* 分页逻辑  */       
        
        //当总数小于显示的页码数
        if ($this->totalPages <= $this->rollPage) {
        	$start = 1;
        	$end   = $this->totalPages;
        }
        else{
        	//
        	if  ($this->nowPage <= $this->rollPage - 1) {
        		$start = 1;
        		$end   = $this->rollPage;
        		
        		$islast = true;
        	}  
        	else if ($this->nowPage > $this->totalPages - $this->rollPage + 1) {
        		$start = $this->totalPages - ($this->rollPage - 1);
        		$end   = $this->totalPages;
        		
        		$isfirst = true;
        	}
        	else{
        		//浮动数
        		$size = floor($this->rollPage / 2);
        		
        		$start = $this->nowPage - $size;
        		$end   = $this->nowPage + $size;
        		
        		$isfirst = true;
        		$islast = true;
        	}
        }
        
        //上下翻页字符串
        $upRow   = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;
        
        
        /* 拼装HTML */
        
        //< 1...     ...last >
        if ($isfirst){
        	$theFirst = "<a class='ue-link' href='".$url."&".$p."=1' >".$this->config['first']."</a>";
        }
        if ($islast){
        	$theEnd = "<a class='ue-link' href='".$url."&".$p."=$this->totalPages' >".$this->config['last']."</a>";
        }
        
        if ($upRow > 0){
        	$upPage = "<a class='ue-prev ue-btn-gray' href='".$url."&".$p."=$upRow'><span><em>".$this->config['prev']."</em></span></a>";
        }
        
        if ($downRow <= $this->totalPages){
        	$downPage = "<a class='ue-next ue-btn-gray' href='".$url."&".$p."=$downRow'><span><em>".$this->config['next']."</em></span></a>";
        }
        
        
        //1 2 3 4 5
        for($i=$start;$i<=$end;$i++){
        	if($i!=$this->nowPage){
        		$linkPage .= "&nbsp;<a class='ue-link' href='".$url."&".$p."=$i'>".$i."</a>";
        	}else{
        		$linkPage .= "&nbsp;<span class='active'>".$i."</span>";
        	}
        }
      
        $pageStr = str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);

        //显示模式  普通false 带跳转ture
        if (empty($this->config['redirect'])){
        	$html = $pageStr;
        
        }else{
        	//传递参数
        	if($this->totalPages > 1){
        		$redirect = "<span class='page_right'><form method='get' action=''>{__NOTOKEN__}<input name=".$p." type='text' class='page_text' size='3' maxlength='3' value='" . $this->nowPage ."'/><input type='submit' class='page_btn' value='跳转' />";
        		if ($params){
        			foreach($params as $k => $v){
        				$string .= "<input type='hidden' name='" . $k . "' value='" . $v . "'/>";
        			}
        			$redirect = $redirect . $string . '</form></span>';
        		}else{
        			$redirect = $redirect . '</form></span>';
        		}
        	}
        	//生成Html字符串
        	$html = $redirect . "<span class='page_right'>" . $pageStr . '</span>';       
        }  
        return $html;
    }

}
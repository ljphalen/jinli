<?php
/**
* 通用的树型类，可以生成任何树型结构
*/
class tree
{
	/**
	* 生成树型结构所需要的2维数组
	* @var array
	*/
	var $arr = array();

	/**
	* 生成树型结构所需修饰符号，可以换成图片
	* @var array
	*/
	var $icon = array('│','├','└');

	/**
	* @access private
	*/
	var $ret = '';

	/**
	* 构造函数，初始化类
	* @param array 2维数组，例如：
	* array(
	*      1 => array('id'=>'1','upid'=>0,'name'=>'一级栏目一'),
	*      2 => array('id'=>'2','upid'=>0,'name'=>'一级栏目二'),
	*      3 => array('id'=>'3','upid'=>1,'name'=>'二级栏目一'),
	*      4 => array('id'=>'4','upid'=>1,'name'=>'二级栏目二'),
	*      5 => array('id'=>'5','upid'=>2,'name'=>'二级栏目三'),
	*      6 => array('id'=>'6','upid'=>3,'name'=>'三级栏目一'),
	*      7 => array('id'=>'7','upid'=>3,'name'=>'三级栏目二')
	*      )
	*/
	function tree($arr=array())
	{
       $this->arr = $arr;
	   $this->ret = "";
	   return is_array($arr);
	}

    /**
	* 得到父级数组
	* @param int
	* @return array
	*/
	function get_parent($myid)
	{
		$newarr = array();
		if(!isset($this->arr[$myid])) return false;
		$pid = $this->arr[$myid]['upid'];
		$pid = $this->arr[$pid]['upid'];
		if(is_array($this->arr))
		{
			foreach($this->arr as $id => $a)
			{
				if($a['upid'] == $pid) $newarr[$id] = $a;
			}
		}
		return $newarr;
	}

    /**
	* 得到子级数组
	* @param int
	* @return array
	*/
	function get_child($myid)
	{
		$a = $newarr = array();
		if(is_array($this->arr))
		{
			foreach($this->arr as $id => $a)
			{
				if($a['upid'] == $myid) $newarr[$id] = $a;
			}
		}
		return $newarr ? $newarr : false;
	}

    /**
	* 得到当前位置数组
	* @param int
	* @return array
	*/
	function get_pos($myid,&$newarr)
	{
		$a = array();
		if(!isset($this->arr[$myid])) return false;
        $newarr[] = $this->arr[$myid];
		$pid = $this->arr[$myid]['upid'];
		if(isset($this->arr[$pid]))
		{
		    $this->get_pos($pid,$newarr);
		}
		if(is_array($newarr))
		{
			krsort($newarr);
			foreach($newarr as $v)
			{
				$a[$v['id']] = $v;
			}
		}
		return $a;
	}

    /**
	* 得到树型结构
	* @param int ID，表示获得这个ID下的所有子级
	* @param string 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
	* @param int 被选中的ID，比如在做树型下拉框的时候需要用到
	* @return string
	*/
	function get_tree($myid,$str="",$sid=0,$adds='')
	{
		if(empty($str)) $str = "<option value='\$id' \$selected>\$spacer\$name</option>";
		
		$number=1;
		$child = $this->get_child($myid);
		if(is_array($child))
		{
		    $total = count($child);
			foreach($child as $id=>$a)
			{
				$j=$k='';
				if($number==$total){
					$j .= $this->icon[2];
				}else{
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				$selected = $child[$id]['id']==$sid ? ' selected' : '';
				@extract($a);
				@eval("\$nstr = \"$str\";");
				$this->ret .= $nstr;
				$this->get_tree($id,$str,$sid,$adds.$k.'&nbsp;');
				$number++;
			}
		}
		return $this->ret;
	}
}
?>
<?PHP
function form_checkbox($params=NULL){
	$params=form__trim_param($params,'label');
	if(element('checked',$params)) $params['checked']='checked';
	//else $params['checked'];
	$params['type']='checkbox';
	return form_input($params);
}

function form_radio($params=NULL){
	$params=form__trim_param($params,'label');
	$params['type']='radio';
	$options = element('options',$params);
	$default = element('default', $params);
	unset($params['options']);
	$value = element('value',$params);
	if ($value == ''){
		$value = $default;
	}
	$html = '';
	foreach($options as $key=>$option){
		if($value==$key) $params['checked']='checked';
		else unset($params['checked']);
		$params['value']=$key;
		$params['label']=$option;
		$html.=form_input($params);
	}
	return $html;
}

function form_label($params=NULL){
	$params=form__trim_param($params);
	$child=element('child',$params);
	$return=element('return',$params);
	$html=form__tag(array('tag_name'=>'label','attr'=>$params,'child'=>$child,'return'=>TRUE));
	if(!$return) return $html;
	echo $html; return "";
}

function form__tag($params=NULL){
	if(!$params) return;

	$params=form__trim_param($params,'tag_name');

	$tag=element('tag_name',$params);
	$attr=element('attr',$params);
	$child=element('child',$params);
	$return=element('return',$params);

	remove_element(array('tag_name','child','return','attr'),$attr);

	$attr=form__wrap($attr);

	if(is_array($attr)) $attr=form__wrap($attr);
	if(!is_scalar($attr)) return "";

	$html='';
	if(is_array($child)){
		if(!$child['multiple']) $children=array($child);
		else $children=$child;
		remove_element('multiple',$children);
		while($child=array_shift($children)){
			if(is_array($child)) $child['return']=TRUE;
			$html.=form_element($child);
		}
	}else $html=$child;
	if($attr) $attr=' '.$attr;
	$html='<'.$tag.$attr.'>'.$html.'</'.$tag.'>';

	if($return) return $html;
	echo $html; return "";
}

function form__wrap($subject,$glue=' '){
	if(is_scalar($subject)) return str_replace('"','&quot;',$subject);
	//just only support array|scalar
	if(!is_array($subject)) return '';
	$html='';
	foreach($subject as $k=>$v){
		$conj=$glue;
		if(!is_numeric($k)) $html.=$k.'="';
		//accept inner-wrapper
		$wp='form__wrap_'.strtolower($k);
		if(function_exists($wp)) $v=$wp($v);
		$html.=form__wrap($v,$glue);
		if(!is_numeric($k)) $html.='"';
		$html.=$glue;
	}
	return $html;
}


function form__trim_param($param,$default_key='name'){
	if($param===NULL) return $param=array();
	if(is_string($param)) return $param=array($default_key=>$param);
	if(!is_array($param)) return array();
	return $param;
}

/**
 *	日期控件
 *
 *	@param	$config
 *		[key	yearRange:String][default:'c-10:c+10']	候选年区间范围
 *		[key	defaultDate:String]default:@today]			默认日期
 *		[key	other-options...]												其他datepicker的控制参数，详情参照jQuery Widget文档说明
 *
 *		[key	value:mixed][default:'']								显示日期值
 *
 *	@example
 *		form_date(array('yearRange'=>'2005-2014'));	设置年份范围
 *		form_date(array('showWeek'=>false,'changeYear'=>false));		不显示周，并且不允许直接修改年份
 *	@return String
 */
function form_date($config=array()){
	static $date_index=1;
	//检测参数是否合法
	if(is_array($config)===FALSE) return FALSE;

	$options=array();

	//设置datepicker的默认控制参数
	$config=array_merge(array('yearRange'=>'c-5:c+5'),$config);

	//获取显示日期
	$value=element('value',$config);
	$name = element('name',$config);
	$class = element('class', $config);
	$minDate = element('minDate', $config);
	$maxDate = element('maxDate', $config);
	if (!$minDate){$minDate = NULL;}else{$minDate = $minDate;}
	if (!$maxDate){$maxDate = NULL;}else{$maxDate = $maxDate;}
	remove_element('value',$config);
	remove_element('name',$config);

	//装换ＰＨＰ数组为ＪＳＯＮ编码
	$options = json_encode($config);

	$date_id='mki_date_'.$date_index++;
	$html = form_input(array('id'=>$date_id,'class'=>$class.' text','value'=>$value,'name'=>$name,'readOnly'=>true));

	//绑定脚本
	$html.='<script type="text/javascript">';
	$html.= "$(function(){";
	$html.= "$('#".$date_id."').datepicker({ minDate: '".$minDate."', maxDate: '".$maxDate."' });";
	$html.= "});";
	$html.='</script>';

	if($date_index===2){
//		$html.=javascript_tag(javascript_path('jquery.ui.datepicker.js'));
//		$html.=javascript_tag(javascript_path('jquery.ui.core.js'));
//		$html.= link_tag(css_path('jquery.ui.theme.css'));
//		$html.= link_tag(css_path('jquery.ui.datepicker.css'));
	}
	return $html;
}

/**
 *	选择控件
 *
 *	@param	$data			Array()		列表值
 *	@param	$config		Array()		配置参数
 *		[key	rule:String]	指定$data数组中的Key-Value在控件中的解析规则，如下：
 *			数组的key值引用用'k'，value值引用用'v'，规则的格式为[option的value属性:option的caption属性]，
 *			各属性可以用引用关键字引用数组项的相应属性。
 *		[key	width:Integer]	宽度，只能为数字，不能为百分比，即不能设置为%xx格式
 *		[key	change:String]	选择发生改变时，执行的JS动作，执行函数可接收一个参数，代表当前选择的值
 *		[key	value:mixed]		默认值
 */
function form_select($data=array(),$config=array()){
	static $select_index=1;
    
	$config = array_merge(array('rule'=>'k:v','width'=>'120','change'=>FALSE,'group'=>FALSE),$config);

	$event_change = element('change',$config);
	$value = element('value',$config);
	$class = element('class', $config);

	$config['width'] = preg_replace('/([^\d]*)(\d+)(.*)/','$2',$config['width']);

	$select_id='mki_select_'.$select_index++;

	$html="<select id='{$select_id}' name='{$config['name']}' class='{$class}' style='width:{$config['width']}px'>";
	$val=substr($config['rule'],0,1);
	$cap=substr($config['rule'],2,1);
	if( empty($data) ) {
        $html.='</select>';
        return $html;
    }
	if($config['group'] == TRUE){
		foreach($data as $gk=>$gv){
			$gmval=str_replace("'",'&#039;',$gk);
			$html .= "<optgroup label='{$gmval}'>";
			foreach ($gv as $k => $v){
				$mval=str_replace("'",'&#039;',$$val);
				$mcap=str_replace(array('<','>'),array('&lt;','&gt;'),$$cap);
				$selected=($$val==$value)?"selected":"";
				$html.="<option value='{$mval}' {$selected}>{$mcap}</option>";
			}
			$html .= "</optgroup>";
		}
		$html.='</select>';
	}else{
		if(!empty($data)){
			foreach($data as $k=>$v){
				$mval=str_replace("'",'&#039;',$$val);
				$mcap=str_replace(array('<','>'),array('&lt;','&gt;'),$$cap);
				$selected=($$val==$value)?"selected":"";
				$html.="<option value='{$mval}' {$selected}>{$mcap}</option>";
			}
		}
		$html.='</select>';
	}
	//绑定脚本
	if($event_change){
		$html.='<script type="text/javascript">';
		$html.="$(function(){";
		//检测是否需要注册值改变事件
		$html.="$('#".$select_id."').change(function(){var val=$(this).val();location.href='".site_url('main/index')."/platform/'+val});";
		$html.='})';
		$html.='</script>';
	}
	return $html;
}
<?php
class OptionsModel extends SystemModel
{
	protected $trueTableName = 'think_options';
	protected $tablePrefix = 'think_';
	public $options;
	
	var $attr = array('name'=>'', 'selected'=>'', 'class'=>'combox');
	
	function attr($name, $value)
	{
		$this->attr[$name] = $value;
		return $this;
	}
	
	function options($options)
	{
		$this->options = $options;
		return $this;
	}
	
	function _attr()
	{
		foreach ($this->attr as $k => $v)
			$str .= sprintf(' %s="%s"', $k, trim($v));
		
		return $str;
	}
	
	function form($optionName, $selected=NULL, $inputType="_select")
	{
		return $this->$inputType($optionName, $selected);
	}
	
	private function get_options($optionName=NULL)
	{
		if(empty($this->options))
		{
			$options = $this->where(array('name'=>$optionName))->cache(TRUE)->find();
			if(empty($options)) return NULL;
			$this->options = $this->where(array('upid'=>$options['id']))->order(array("sort","id"))->getField("value,name");
		}
	}
	
	function _select($optionName, $selected=NULL)
	{
		if($this->attr['name'] == '')
			$this->attr['name'] = sprintf("%s", $optionName);
	
		if($selected !== NULL)
			$this->attr['selected'] = $selected;
	
		$this->get_options($optionName);

		foreach($this->options as $val => $option)
		{
			$selected = ($this->attr['selected'] != '' && $val == $this->attr['selected']) ? ' selected' : '';
			$html .= sprintf('<option value="%s"%s>%s</option>', $val, $selected, $option);
		}
	
		!empty($html) && $html = sprintf('<select %s><option value="" rel="defaultVal">%s</option>%s</select>', $this->_attr(), $options['desc']?$options['desc']:'请选择', $html);
		return $html;
	}
	
	function _radio($optionName, $selected=NULL)
	{
		if($this->attr['name'] == '')
			$this->attr['name'] = sprintf("%s", $optionName);
	
		if($selected !== NULL)
			$this->attr['selected'] = $selected;
	
		$this->get_options($optionName);
		foreach($this->options as $val => $option)
		{
			$selected = ($this->attr['selected'] != '' && $val == $this->attr['selected']) ? ' checked' : '';
			$html .= sprintf('<label><input type="radio" name="%s" value="%s"%s>%s</label>', $this->attr['name'], $val, $selected, $option);
		}
	
		return $html;
	}
	
	function value($optionName, $value)
	{
		$this->get_options($optionName);
		if(!empty($this->options))
			return $this->options[$value];
		
		$options = $this->where(array('name'=>$optionName))->cache(TRUE)->find();
		return $this->where(array('upid'=>$options['id'], 'value'=>intval($value)))->getField('name');
	}
}
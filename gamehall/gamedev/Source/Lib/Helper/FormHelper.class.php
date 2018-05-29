<?php
/**
 * 一个简单的表单元素生成工具
 * 用法:
 * 		echo Helper("Form")->name("chiose_id")->option(["请选择", 1=>"选项1", 2=>"选项2"])->value(0)->render("select");
 *		echo Helper("Form")->name("chiose_id")->option(["请选择", 1=>"选项1", 2=>"选项2"])->value(1)->render("radio");
 *		echo Helper("Form")->name("chiose_id")->option(["请选择", 1=>"选项1", 2=>"选项2"])->value(2)->render("checkbox");
 *		echo Helper("Form")->name('username')->value("yangshuhai")->placeholder('默认用户名')->render();
 *		echo Helper("Form")->name('username')->value("yangshuhai")->render("button");
 *		echo Helper("Form")->name('username')->value("yangshuhai")->render("submit");
 *		echo Helper("Form")->name('username')->value("yangshuhai")->render("textarea");
 *		echo Helper("Form")->name("chiose_id")->option(["请选择", 1=>"选项1", 2=>"选项2"])->value(2)->select();
 * 
 * @author shuhai
 */
class FormHelper
{
	protected $_tpl          = "";
	protected $_itpl         = "";
	protected $_element      = "text";
	protected $_attributes   = [];
	protected $_element_type = ["radio", "select", "checkbox", "textarea", "button", "submit", "label"];
	protected $_html_element_type = ["div", "span"];

	function __call($name, $arguments)
	{
		if(count($arguments) == 1)
			$arguments = $arguments[0];
		return $this->attr($name, $arguments);
	}

	//设置html元素属性
	function attr($name, $arguments)
	{
		if(in_array($name, $this->_element_type))
		{
			$this->_element = $name;
			return $this->render($name);
		}else if(in_array($name, array("tpl", "itpl"))){
			$this->{"_".$name} = $arguments;
		}else{
			$this->_attributes[$name] = $arguments;
		}

		return $this;
	}
	
	function render($type=null)
	{
		if(isset($type))
			$this->_element = $type;

		if(!in_array($this->_element, $this->_element_type))
			$this->_element = "text";
		
		if(in_array($this->_element, ["button", "submit"]))
			return $this->render_text();

		$render = $this->{"render_".$this->_element}();
		
		//清空对象
		$this->_attributes = [];
		
		return empty($this->_tpl) ? $render : sprintf($this->_tpl, $render);
	}

	/**
	 * 渲染select
	 * @return string
	 */
	function render_select()
	{
		$default_val = isset($this->_attributes["default_value"]) ? $this->_attributes["default_value"] : "";
		$default = isset($this->_attributes["default"]) ? "<option value=\"{$default_val}\">{$this->_attributes['default']}</option>" : "<option value=\"{$default_val}\">请选择</option>";
		return sprintf("<select%s>%s%s</select>", $this->get_attributes(['option', 'value', 'default']), $default, $this->get_select_value());
	}
	
	function render_text()
	{
		return sprintf("<div class=\"input-group\">%s<input type=\"{$this->_element}\"%s></div>", $this->render_label(), $this->get_attributes(['label']));
	}
	
	function render_label()
	{
		if(!isset($this->_attributes["label"])) return '';
		return sprintf("<label for=\"%s\">%s<label>", $this->_attributes["name"], $this->_attributes["label"]);
	}
	
	function render_radio()
	{
		$radio = sprintf("<input type=\"{$this->_element}\"%s%%s>%%s", $this->get_attributes(['option', 'value']));
		$attr = $item = '';
		foreach ($this->_attributes["option"] as $k=>$v)
		{
			$check = (isset($this->_attributes["value"]) && $this->_attributes["value"] == $k) ? ' checked' : '';
			$item  = sprintf($radio, " value=\"{$k}\"{$check}", $v);
			$attr .= empty($this->_itpl) ? $item : sprintf($this->_itpl, $item);
		}
		
		return $attr;
	}
	
	function render_checkbox()
	{
		return $this->render_radio();
	}
	
	function render_textarea()
	{
		return sprintf("<textarea%s>%s</textarea>", $this->get_attributes(), $this->_attributes["value"]); 
	}
	
	/**
	 * 拼接html属性
	 * @param array $attrs  指定属性
	 * @param bool  $isOnly 过滤指定属性，或者仅限于指定属性
	 * @return string
	 */
	protected function get_attributes(array $attrs=[], $isOnly=false)
	{
		$attr = '';
		foreach ($this->_attributes as $k=>$v)
		{
			if($isOnly && !in_array($k, $attrs)) continue;
			if($isOnly == false && in_array($k, $attrs)) continue;

			if(!is_array($v))
				$attr .= sprintf(' %s="%s"', $k, $v);
		}
		return $attr;
	}
	
	/**
	 * 格式化select表单的选项
	 * @return string
	 */
	protected function get_select_value()
	{
		$attr = '';
		foreach ($this->_attributes["option"] as $k=>$v)
		{
			$attr .= sprintf('<option value="%s"%s>%s</option>',
					$k,
					strlen($this->_attributes["value"]) == strlen($k) && ($this->_attributes["value"] == $k) ? ' selected="selected"' : '',
					$v);
		}
		return $attr;
	}
}
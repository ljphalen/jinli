<?php
/**
 * 默认操作和空操作均引导至用户中心
 */
class EmptyAction extends SystemAction
{
	function _empty()
	{
		try {
			$this->display();
		} catch (Exception $e) {
			//地区联动需要进行跨域，放到后台模块中访问
			if(strtolower(Misc) == 'misc')
				return A("Dev://Misc")->area_show();
			
			if(APP_DEBUG)
			{
				throw_exception('[空操作]'.$e->getMessage());
			}else{
				halt('非法操作了噻');
			}
		}
	}
}
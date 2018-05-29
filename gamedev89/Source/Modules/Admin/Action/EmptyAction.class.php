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
			if(APP_DEBUG)
			{
				throw_exception('[空操作]'.$e->getMessage());
			}else{
				halt('非法操作了噻');
			}
		}
	}
}
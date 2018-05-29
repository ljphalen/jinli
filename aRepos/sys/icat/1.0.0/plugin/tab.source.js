(function($, iCat, undefined){
	
	/** 默认结构如下：
	 * <!-- html code -->
	 *	<div id="J_tabBox">
	 *		<ul class="J_tabHandle">
	 *			<li></li>
	 *			<li></li>
	 *			<li></li>
	 *		</ul>
	 *		<div>
	 *			<div class="J_tabContent"></div>
	 *			<div class="J_tabContent hidden"></div>
	 *			<div class="J_tabContent hidden"></div>
	 *		</div>
	 *	</div>
	 * <!-- html code --> */
	
	iCat.widget.tab = function(config){
		var defaultConfig = {
			tabHandle: '.J_tabHandle li',
			tabSelected: 'selected',
			tabContent: '.J_tabContent',
			context: '#J_tabBox',
			eventType: 'click'
		},
		
		//配置列表
		cfg = $.extend(defaultConfig, config);
		if(!$(cfg.tabHandle).length || !$(cfg.tabContent).length) return;
		
		//操作元素
		var $li = !cfg.context ?
						$(cfg.tabHandle) : $(cfg.tabHandle, $(cfg.context)),
			$div = !cfg.context ?
							$(cfg.tabContent) : $(cfg.tabContent, $(cfg.context)),
		
		//效果函数
		_tab = function(evt){
			evt.preventDefault();
			var idx = $li.index(this);
			
			$(this).addClass(cfg.tabSelected).siblings().removeClass(cfg.tabSelected);
			$div.hide().eq(idx).show();
		};
		
		
		switch(cfg.eventType){
			
			case 'hover':
				$li.bind('mouseover', _tab);
				$li.bind('mouseout', function(){});
				break;
				
			default:
				$li.bind('click', _tab);
				break;
		}
	}
})(jQuery,ICAT);
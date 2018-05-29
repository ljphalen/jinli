(function(iCat,$){
	iCat.add('tab', function(){
		var tab = {
			init: function(config){
				var defConfig = {
					context: '#J_tabBox',
					handle: '.J_handle li',
					main: '.J_main',
					onCla: 'selected',
					eType: 'click',
					isHidden: true,
					callback: function(){}
				},
				cfg = $.extend(defConfig, config),
				oTabBox = $(cfg.context),
				oHandle = oTabBox.find(cfg.handle),
				oMain = oTabBox.find(cfg.main);
				
				if(!oHandle.length || !oMain.length) return;
				tab.tabfn(oHandle, oMain, cfg.eType, cfg.onCla, cfg.callback, cfg.isHidden);
			},
			
			tabfn: function(oHandle, oMain, eType, onCla, callback, isHidden){
				oHandle.bind(eType, function(evt){
					evt.preventDefault();
					
					var i = oHandle.index(this);
					oHandle.removeClass(onCla).eq(i).addClass(onCla);
					isHidden? oMain.addClass('hidden').eq(i).removeClass('hidden') : oMain.hide().eq(i).show();
					callback.call(this);
				});
			}
		};
		
		iCat.tab = tab.init;
	});
})(ICAT, jQuery);
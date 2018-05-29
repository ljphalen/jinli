(function(iCat, doc){
	var curPage = doc.body.className, events;
		
	if(!curPage) return;

	//model
	iCat.Model(curPage, GNnav.initdata);
	
	var pageM = iCat.Model[curPage],
		pageData = pageM.getInitData(curPage);

	pageM.extend({
		_render: function(curMod, data, cb){
			data = iCat.mix(data, curMod.mixData);

			iCat.View[curMod.mod]?
				iCat.View[curMod.mod].setData(data) : iCat.View(curMod.mod, data);

			if(cb) cb();
		},

		modRender: function(storageIndex, curModule, curWrap, cb){
			//if(/super\.|client\./i.test(location.host) && curModule.mod==='aHeader') return;
			if(curModule.ajaxUrl){
				var keyStorage = '__icat_' + curModule.mod + storageIndex;
				if(navigator.onLine==true){
					$.ajax({
						type: 'POST', timeout:10000,
						url: curModule.ajaxUrl + storageIndex,
						//data: iCat.isDemoMode? {} : {token:token},
						cache: false,
						success: function(d){
							if(!d) return;
							iCat.Model.storage(keyStorage, d);
							//pageM.clearStorage(localStorage);
							var data = JSON.parse(d);
							curWrap.addClass('done');
							curModule.mixData.parentWrap = curWrap[0];
							pageM._render(curModule, data, cb);				
						},
						error: function(){
							if(iCat.Model.storage(keyStorage)==null) return;
							var data = iCat.Model.storage(keyStorage);
								data = JSON.parse(data);
							pageM._render(curModule, data, cb);	
						}
					});
				} else {
					if(iCat.Model.storage(keyStorage)==null) return;
					var data = iCat.Model.storage(keyStorage);
						data = JSON.parse(data);
					pageM._render(curModule, data, cb);	
				}
			} else {
				pageM._render(curModule, {}, cb);	
			}

			//iCat.View.destroy(curModule.mod);
		},

		showDetail: function(index){
			if(index==undefined) return;

			var _mod = pageData.CommModule['detail'],
				_self = $('.J_nav_title').eq(index), id = _self.data('id'),
				_cur = _self.next('.J_nav_content').children('.ui-nav-ul');
			//if(!_cur.hasClass('done')){
				pageM.modRender(id, _mod, _cur, function(d){});
			//}
		}
	});

	//controler
	iCat.Controller(curPage, function(pageC){
		if(!pageData) return;

		//view
		var initMods = pageData.InitModule,
			navIndex = initMods.list.mixData.curIndex;

		if(initMods && !iCat.isEmptyObject(initMods)){
			iCat.foreach(initMods, function(k, item){
				iCat.isArray(item) || iCat.isString(item) ?
					iCat.util.addModsWrap(item) : pageM.modRender(k, item);
			});
		}
		//根据上次展开的模块，默认显示数据
		pageM.showDetail(0);
		pageM.showDetail(1);
		pageM.showDetail(2);
		pageM.showDetail(3);
		pageM.showDetail(4);
		pageM.showDetail(5);
		//如果是最后一个模块
		//if(navIndex == $('.J_nav_content').length-1){
		//	$('.J_nav_title').eq(navIndex).addClass('ui-nav-last');
		//}

		//$(document.body).scrollTop($('.J_nav_title').eq(0).height() * navIndex + 11);
	});

	switch(curPage){
		case 'home':
			events = [
				{
					el:'.J_nav_title', eType:'singleTap', stopPropagation:true, preventDefault:true,
					callback: function(){
						var that = $(this), navWrap = $(".J_nav_title"), contentWrap = $(".J_nav_content"),
							selLast  = that.data('id'),
							curNext  = that.next(),
							curIndex = navWrap.index(that);

						selLast == navWrap.length-1 ? that.addClass('ui-nav-last') : '';
						pageM.showDetail(curIndex);
						iCat.Model.cookie('navIndex', curIndex, 3600);

						if(curNext.hasClass('ishide')){
							contentWrap.addClass('ishide');
							curNext.removeClass('ishide');
							navWrap.removeClass('ui-nav-arrow');
							that.addClass('ui-nav-arrow');
						} else {
							curNext.addClass('ishide');
							that.removeClass('ui-nav-arrow');
						}

						$(document.body).scrollTop(that.height() * curIndex + 11);
					}
				}
			];
			break;
	}



    /*$.fn.highlight = function(className) {
	    var actElem, inited = false, timer, cls, removeCls = function(){
	        clearTimeout(timer);
	        if(actElem && (cls = actElem.attr('highlight-cls'))){
	            actElem.removeClass(cls).attr('highlight-cls', '');
	            actElem = null;
	        }
	    };

        inited = inited || !!$(document).on('touchend.highlight touchmove.highlight touchcancel.highlight', removeCls);
        className && $(this).delegate('a','touchstart.highlight',function(){
        	var that = $(this);
            timer = setTimeout(function() {
                    actElem = that.attr('highlight-cls', className).addClass(className);
            }, 100);

    	});
    }
 	
 	$('.J_nav_content').highlight('highlight');
 	*/

	iCat.Controller[curPage];
	iCat.Controller.addCurrent(curPage);
})(ICAT, document);
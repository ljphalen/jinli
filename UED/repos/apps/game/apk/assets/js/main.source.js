;(function(iCat){
	iCat.PathConfig();
	iCat.app('GameApk', function(){
		function getVersion(){
			var search=window.location.search;
			var version="";
			var spParam="";
			if(!search) return 'v1';
			else{
				search=search.substring(1);//截除问号
				var items=search.split('&');
				for(var i=0;i<items.length;i++){
					if(!items[i]){
						continue;
					}
					if(items[i].split('=')[0]=='sp'){
						spParam=items[i].split('=')[1];//取得sp参数
						break;
					};
				}
			}
			if(!spParam) return 'v1';
			else if(spParam.split('_').length<2||spParam.split('_')[1]=='') return 'v1';
			else{
				version=spParam.split('_')[1];
				if(version.indexOf('1.4.8')>-1) return 'v2';//
				return 'v1';
			}
		}
		iCat.ModConfig({
			'$jQuery':'../../lib/jquery/jquery.js',
			'phoneGap':'./plugin/cordova-2.0.0.js',
			'$touchSwipe':'../../lib/jquery/touchSwipe.js'
		})
		return {
		ApiVersion:getVersion(),//v1 页面调用旧版接口及旧版视觉 v2 即大于1.4.8的版本 调用新版接口及视觉
		blankPic:(iCat.PathConfig.staticRef+iCat.PathConfig.appRef).replace(/assets\/js\//g, 'pic/blank.gif'),
		loadingPic:(iCat.PathConfig.staticRef+iCat.PathConfig.appRef).replace(/assets\/js\//g, 'pic/loading_img.gif')
		};
	});
	iCat.require({
		modName:'appmvc',domReady:false,isCombo:!(iCat.DebugMode||iCat.DemoMode),
		callback:function(){
			 var c=new GameApk.controller('mc');
			iCat.include(['jQuery','phoneGap','./game.js'],function($){
					c.bindEvents();
					Gapk.init();
			},true,!(iCat.DebugMode||iCat.DemoMode));
		}
	});
})(ICAT);
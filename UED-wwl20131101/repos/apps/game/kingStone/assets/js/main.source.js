;(function(iCat){
	iCat.PathConfig();
	iCat.app('GameApk', function(){
		function getVersion(){
			var search=window.location.search,spParam="";
			if(!search) return {version:'v1',sp:spParam};
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
			if(!spParam) return {version:'v1',sp:spParam};
			else if(spParam.split('_').length<2||spParam.split('_')[1]=='') return {version:'v1',sp:spParam};
			else{
				var vNumbers=spParam.split('_')[1].split('.');
				//大于1.4.7的版本为V2，小于等于则为v1
				if(vNumbers[0]>1||(vNumbers[0]==1&&vNumbers[1]>4)||(vNumbers[0]==1&&vNumbers[1]==4&&vNumbers[2]>7)) {
					return {version:'v2',sp:spParam};
				}
				return {version:'v1',sp:spParam};;
			}
		}
		iCat.ModConfig({
			'$jQuery':'../../lib/jquery/jquery.js',
			'phoneGap':'./plugin/cordova-2.0.0.js',
			'$touchSwipe':'../../lib/jquery/touchSwipe.js'
		})
		return {
		spParam:getVersion().sp,
		ApiVersion:getVersion().version,//v1 页面调用旧版接口及旧版视觉 v2 即大于1.4.8的版本 调用新版接口及视觉
		blankPic:iCat.PathConfig.picPath+'blank.gif',
		loadingPic:iCat.PathConfig.picPath+'loading_img.gif'
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
;(function(iCat){
	isAla=location.href.indexOf('ala')>-1?true:false;
	iCat.PathConfig();
	var blankpic;
	var loadingpic;
	if(isAla){
		blankpic=iCat.PathConfig.appRef.replace(/assets\/js\//g, 'pic/blank.gif');
		loadingpic=iCat.PathConfig.appRef.replace(/assets\/js\//g, 'diff/ala/img/loading_new.gif');
		iCat.include('../../diff/ala/css/patch.css');
	}
	iCat.app('GameApk', function(){
		return {
		version: 'ala_1.4.7',
		blankPic:blankpic,
		loadingPic:loadingpic
		};
	});
	iCat.include(['./plugin/cordova-2.0.0.js!','lib/jquery/jquery.js'], function(){
		iCat.require({modName:'appMVC', callback: function(){
				iCat.include('./game.js', function(){
					Gapk.init();
				},true);
			}
		});
	},true);
})(ICAT);
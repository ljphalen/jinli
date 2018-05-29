(function(iCat){
	var header='',
		layout='div#container';
	GameApk.controller = iCat.Controller.extend(
	{
		config:{
			View:GameApk.view,
			Model:GameApk.model
		},
		routes:{
			'home':'homeInit',//首页
		},
		homeInit: function(){
			var c = this;
			c.init({
				baseBed: '.module',
				adjustLayout:layout,
				modules:'slidePic,recItem,recommend'//
			});
		}
	});
})(ICAT);
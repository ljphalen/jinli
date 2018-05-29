(function(iCat){
	var header='',
		layout='div#container';
	var Controller = iCat.Controller.extend(
	{
		config:{
			baseBed:'.module'
		},
		routes:{
			'home':'homeInit',//首页
		},
		homeInit: function(){
			var c = this;
			c.init({
				view: GameApk.view, model: GameApk.model,
				adjustLayout:layout,
				modules:header+'slidePic,recItem,recommend'
			});
		},
	});
	new Controller('mainPage');
})(ICAT);
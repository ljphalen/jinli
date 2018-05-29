(function(iCat){
	var header='',
		layout='div.module';
	GameApk.controller = iCat.Controller.extend(
	{
		config:{
			View:GameApk.view,
			Model:GameApk.model
		},
		routes:{
			'home':'homeInit',//首页
			'category':'categoryInit',//分类
			'subject':'subjectInit'//专题
		},
		homeInit: function(){
			var c = this;
			c.init({
				baseBed: 'body#home',
				adjustLayout:layout,
				modules:GameApk.ApiVersion=='v1'?'slidePic,recItem,recommend':'slidePic,recItem,activity,recommend',
			})
		},
		categoryInit:function(){
			var c=this;
			c.init({
				baseBed:'body#category',
				adjustLayout:'div.module',
				modules:'category'
			})
		},
		subjectInit:function(){
			var c=this;
			c.init({
				baseBed:'body#subject',
				adjustLayout:'div.module',
				modules:'subject'
			})
		}
	});
})(ICAT);
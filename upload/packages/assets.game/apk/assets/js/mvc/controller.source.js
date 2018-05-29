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
			'subject':'subjectInit',//专题
			'rank':'rankInit',//排行
			'new':'newInit',//最新
			'event':'eventInit'//活动
		},
		homeInit: function(){
			var c = this;
			c.init({
				baseBed: 'body',
				adjustLayout:layout,
				modules:GameApk.ApiVersion=='v1'?'slidePic,recItem,recommend':'slidePic,recItem,activity,recommend'
			})
		},
		categoryInit:function(){
			var c=this;
			c.init({
				baseBed:'body',
				adjustLayout:'div.module',
				modules:'category'
			})
		},
		subjectInit:function(){
			var c=this;
			c.init({
				baseBed:'body',
				adjustLayout:'div.module',
				modules:'subject'
			})
		},
		rankInit:function(){
			var c=this;
			c.init({
				baseBed:'body',
				adjustLayout:layout,
				modules:'commonItem',
			})
		},
		newInit:function(){
			var c=this;
			c.init({
				baseBed:'.timeline',
				adjustLayout:layout,
				modules:'newItem',
			})
		},
		eventInit:function(){
			var c=this;
			c.init({
				baseBed:'body',
				adjustLayout:'div.module',
				modules:'eventSection'
			})
		},
	});
})(ICAT);
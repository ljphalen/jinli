(function(iCat){
	var Controller = iCat.Controller.extend(
	{	
		config: {baseBed: '.module'},
		routes: {
			'home': 'homeInit',
			'cod': 'codInit',
			'shops': 'shopsInit',
			'new': 'newInit',
			'mall': 'mallInit'
		},

		gotop: function(){
			window.onscroll = function(){
				if(document.body.scrollTop < 50){
					$('.gotop').hide();
				} else {
					$('.gotop').show();
				}
			}
		},

		homeInit: function(){
			var c = this;

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: 'div#iScroll',
				modules: 'gotop, banner, points, notice, mall, theme, tuan, helper, footer'
			});

			c.gotop();

		},

		codInit: function(){
			var c = this;

			c.init({
				view: Gou.view, model: Gou.model,
				adjustLayout: 'div#iScroll',
				modules: 'gotop, codSearch, list'
			});

			c.gotop();
		},

		shopsInit: function(){
			var c = this;

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: 'div#iScroll > div.panel.shop-info',
				modules: 'gotop, shopList'
			});

			c.gotop();
		},

		newInit: function(){
			var c = this;

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: 'div#iScroll',
				modules: 'gotop, newlist'
			});

			c.gotop();
		},

		mallInit: function(){
			var c = this,
				sheader = Gou.view.setting.subheader;
			iCat.mix(sheader.config,
			{
				ajaxUrl: '/api/apk_fitting/type',
				hooks: {'&>0':'.parts-nav.J_partsNav'}
			});

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: 'header#iHeader.hd + div#iScroll',
				modules: 'gotop, subheader, partslist'
			});

			c.gotop();
		}
	});
	
	new Controller('mainPage');
})(ICAT);
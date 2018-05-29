(function(iCat){
	var header, layout, mwebInit, mapkInit, isWeb, isApp;
	isWeb = /super\.|client\.|app\./i.test(location.host) ? false : true;
	isApp = /app\./i.test(location.host) ? true : false;

	if(isWeb){
		header = 'header,';
		layout = 'header#iHeader.hd + div#iScroll';
		mwebInit = "topBanner, header, gotop, recommend, notice, mall, theme, tuan, helper, footer";
	} else if (!isWeb && !isApp) {
		header = '';
		layout = 'div#iScroll';
		mapkInit = "gotop, banner, points, notice, mall, theme, tuan, helper, footer1";
	} else if (!isWeb && isApp) {
		header = 'header,';
		layout = 'header#iHeader.hd + div#iScroll';
		mapkInit = "header, gotop, recommend, notice, mall, theme, tuan, helper, footer1";
	}

	var Controller = iCat.Controller.extend(
	{	
		config: {baseBed: '.module'},
		routes: {
			'home': 'homeInit',
			'cod': 'codInit',
			'shops': 'shopsInit',
			'new': 'newInit',
			'mall': 'mallInit',
			'jhnews': 'jhnewsInit'
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
			var c = this,
				nav = Gou.view.setting.header.nav;
			nav.forEach(function(v, i){
				i==0? v.selected = true : delete v.selected;
			});

			c.init({
				view: Gou.view, model: Gou.model,
				//scrollBox: document,
				adjustLayout: (isWeb? 'div.top-banner+':'') + layout,
				//modules: (isWeb? 'topBanner, ':'') + header + 'banner, recommend, points, notice, mall, theme, tuan, helper, footer'
				modules: isWeb ? mwebInit : mapkInit
			});

			c.gotop();

		},

		codInit: function(){
			var c = this,
				nav = Gou.view.setting.header.nav;
			nav.forEach(function(v, i){
				i==1? v.selected = true : delete v.selected;
			});

			c.init({
				view: Gou.view, model: Gou.model,
				adjustLayout: layout,
				scrollBox: document,
				modules: header + 'gotop, codSearch, list'
			});

			c.gotop();
		},

		shopsInit: function(){
			var c = this,
				nav = Gou.view.setting.header.nav;
			nav.forEach(function(v, i){
				i==2? v.selected = true : delete v.selected;
			});

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: layout + ' > div.panel.shop-info',
				modules: header + 'gotop, shopList'
			});

			c.gotop();
		},

		newInit: function(){
			var c = this;
			Gou.view.setting.subheader.subtitle = '新品女装';

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: 'header#iHeader.hd + div#iScroll',
				modules: 'gotop, subheader, newlist'
			});

			c.gotop();
		},

		mallInit: function(){
			// /super\./i.test(location.host)
			var c = this, superHost =  /super\./i.test(location.host) ? true : false,
				sheader = Gou.view.setting.subheader;
			iCat.mix(sheader.config,
			{
				ajaxUrl: '/api/fitting/type',
				hooks: {'&>0:1':'.parts-nav.J_partsNav'}
			});
			sheader.subtitle = '手机配件';

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: superHost ? 'header#iHeader.hd.super-hd + div#iScroll' : 'header#iHeader.hd + div#iScroll',
				modules: 'gotop, subheader, partslist'
			});

			c.gotop();
		},

		jhnewsInit: function(){
			this.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				modules: 'jhnews'
			});
		}
	});
	
	new Controller('mainPage');
})(ICAT);
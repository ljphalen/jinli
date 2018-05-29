(function(iCat){
	var header, layout;
	header = Gou.platform.head || '';
	layout = Gou.platform.layout || 'div#iScroll';

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
			var c = this,
				nav = Gou.view.setting.header.nav;
			nav.forEach(function(v, i){
				i==0? v.selected = true : delete v.selected;
			});

			c.init({
				view: Gou.view, model: Gou.model,
				adjustLayout: layout + ' > div.panel',
				scrollBox: document,
				modules: header + 'gotop, banner, points, notice, mall, theme, tuan, helper, footer'
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

			/*iCat.util.scroll(document, function(slHeight, slTop, spHeight){
				var listWrap = $('#iScroll');
				if(listWrap.attr('data-hasnext')!=='true') return;

				var vData = iCat.Model.ViewData('shopList'), cfg = vData.config,
					curPage = listWrap.attr('data-curpage'),
					curView = iCat.View['shopList'], num = parseInt(curPage) + 1,
					_page = listWrap.attr('data-ajaxurl') + '?page=' + num;
				if(slTop+slHeight+listWrap.find('li').height()>=spHeight && curView['page'+num]===undefined){
					curView['page'+num] = false;

					cfg.callback = function(p, cfg, d){
						listWrap.attr('data-curpage', num);
						listWrap.attr('data-hasnext', d.data.hasnext);
						curView['page'+num] = true;
					};

					curView.setAjaxUrl(_page);
				}
			});*/

			c.gotop();
		},

		newInit: function(){
			var c = this;
			if(/ios\./i.test(location.host)){
				var adjustLayout = 'header#iHeader.hd + div#iScroll > div.panel',
					modules = 'gotop, subheader, newlist';
				Gou.view.setting.subheader.subtitle = '新品女装';
			}

			c.init({
				view: Gou.view, model: Gou.model,
				scrollBox: document,
				adjustLayout: adjustLayout || 'div#iScroll > div.panel',
				modules: modules || 'gotop, newlist'
			});

			c.gotop();
		},

		mallInit: function(){
			// /super\./i.test(location.host)
			var c = this, superHost =  /channel\.|market\./i.test(location.host) ? true : false,
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
				adjustLayout: superHost ? 'header#iHeader.hd.super-hd + div#iScroll > div.panel' : 'header#iHeader.hd + div#iScroll > div.panel',
				modules: 'gotop, subheader, partslist'
			});

			c.gotop();
		}
	});
	
	new Controller('mainPage');
})(ICAT);
GNnav.initdata = {
	home: {
		InitModule: {
			/*
			 * 需调整结构的地方，支持数组和多class/id。
			 * - ~分割，前面是已有模块，后面为新增
			 * - .module元素下新增6个.modChild的div
			 */
			//ModsWrap: '.module:0~div.modChild*1',

			'list': {//精品导航列表
				mod: 'home', //对应的模板
				//ajaxUrl: GNnav.fullUrl('/api/home',true),
				mixData: {
					parentWrap: '.module:0',
					nav:["常用","资讯 新闻","小说 阅读","软件 游戏","生活 购物","视频 娱乐"],
					curIndex:ICAT.Model.cookie('navIndex') ? ICAT.Model.cookie('navIndex') : 0,

					/*
					 * 本父层下的子元素hooks，支持添加多个和多class/id
					 * - key可以是selector形式 或 :隔开的形式
					 * - :n表示元素集合的n元素(n从0计数，0表示第一个元素)
					 * - :*表示循环其前的选择器，来查找其子元素
					 
					hooks: {
						'.item'         : '.J_itemWrap',
						'.item .detail' : '.J_itemDetail'
					},*/
				} 
			},
		},
		CommModule: {
			'detail': {//详情
				mod: 'navItem',
				ajaxUrl: GNnav.fullUrl('/api/nav2/index/index?mid=',true),
				mixData: {}
			}
		}
	}
}; 


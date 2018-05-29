(function(global) {

	// 窗口适配
	var adapterScreen = function() {
		var doc = global.document, 
			root = doc.documentElement,
			currentW = 320, maxWidth = 540, minWidth = 320, timer,
			flexible = function() {
				currentW = root.getBoundingClientRect().width;
				currentW = currentW > maxWidth ? maxWidth : (currentW < minWidth ? minWidth : currentW);
				root.style.fontSize = currentW / 16 + 'px';
			};
		global.addEventListener('resize', function(){
			if (timer) clearTimeout(timer);
			timer = setTimeout(function() { flexible() }, 0);
		}, false)

		flexible();
	}

	// 禁止左右滑屏
	var disableUcGesture = function() {
		var userAgent;
		var control;
		userAgent = navigator.userAgent;
		if (/ucbrowser/i.test(userAgent)) {
			control = navigator.control || {};
			if (control.gesture) control.gesture(false);
		}
	}

	// 百度统计代码
	var baiduStatic = function(id, action, label) {
		try {
			if (!_hmt || !id || !action) return;
			label = label || '';
			_hmt.push(['_trackEvent', id, action, label]);
		} catch(e) {};
	};

	// weinre远程调试代码
	var weinreDebug = function() {
		var node = document.createElement('script');
		node.src = 'http://192.168.115.23:8080/target/target-script-min.js#anonymous'
		document.head.appendChild(node);
	}

	// 开始加载模块
	var startInit = function() {
		var bodyId = document.body.id;
		if (bodyId) {
			require(bodyId.split('|'));
		}
	}
	// 滑动时隐藏/显示header
	var header = $('#J_header');
	var height = header.height();
	var marginTop = '-' + height + 'px';
	var scrollWrap = $('#J_scrollWrap');
	var timer;

	var showHeader = function() {
		header.animate({
			'margin-top': 0
		}, 300, 'ease-out');
	};

	var hideHeader = function() {
		header.animate({
			'margin-top': marginTop
		}, 300, 'ease-out')
	}

	scrollWrap[0].addEventListener('touchstart', function() {
		showHeader();
		if (timer) timer = clearTimeout(timer);
		timer = setTimeout(function() {
			if (scrollWrap.scrollTop() > 0) {
				hideHeader();
			}
		}, 3000);
	}, false);

	adapterScreen();
	disableUcGesture();
	global.baiduStatic = baiduStatic;
	startInit();
	
	// weinreDebug();

})(this);
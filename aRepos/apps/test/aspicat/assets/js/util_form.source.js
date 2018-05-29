/*!
 * 表单相关的各种方法
 * require: http://res.51able.com/sys/jquery.js
 */
 
(function(iCat, util, $){
	iCat.mix(util, {
		/* 密码强度 */
		pwdStrength: function () {
			var _CharMode = function (iN) {
				if (iN >= 48 && iN <= 57) //数字 
					return 1;
				if (iN >= 65 && iN <= 90) //大写字母 
					return 2;
				if (iN >= 97 && iN <= 122) //小写 
					return 4;
				else
					return 8; //特殊字符 
			},

			_bitTotal = function (num) {
				modes = 0;
				for (i = 0; i < 4; i++) {
					if (num & 1) modes++;
					num >>>= 1;
				}
				return modes;
			},

			_checkStrong = function (sPW) {
				if (sPW.length <= 4 || /^\d*$/g.test(sPW))
					return 0; //密码太短或纯数字
				Modes = 0;
				for (i = 0; i < sPW.length; i++) {
					//测试每一个字符的类别并统计一共有多少种模式. 
					Modes |= _CharMode(sPW.charCodeAt(i));
				}
				return _bitTotal(Modes);
			},

			_pwStrength = function (o, pwd) {
				var strCla = ['pwd0', 'pwd1', 'pwd2'],
					$pwdSafe = o.siblings('.J_pwdSafe');
				if (pwd == null || pwd == '') {
					var oldCla = $pwdSafe.attr('data-class');
					$pwdSafe.removeClass(oldCla);
					$pwdSafe.siblings('span').show();
				} else {
					var S_level = _checkStrong(pwd);
					$pwdSafe.siblings('span').hide();
					switch (S_level) {
						case 0:
							var oldCla = $pwdSafe.attr('data-class');
							$pwdSafe.removeClass(oldCla).addClass(strCla[0]);
							$pwdSafe.attr('data-class', strCla[0]);
							break;
						case 1:
							var oldCla = $pwdSafe.attr('data-class');
							$pwdSafe.removeClass(oldCla).addClass(strCla[1]);
							$pwdSafe.attr('data-class', strCla[1]);
							break;
						case 2:
							var oldCla = $pwdSafe.attr('data-class');
							$pwdSafe.removeClass(oldCla).addClass(strCla[2]);
							$pwdSafe.attr('data-class', strCla[2]);
							break;
					}
				}
				return;
			};

			$('.J_pwdStrength').bind('blur keyup', function () {
				_pwStrength($(this), this.value);
			});
		},
		
		/*封装表单元素=》字符串 */
		form2str: function(form){
			if(!form) return;
			var strArguments = '';
			for(var i=0, els=form.elements, ilen=els.length; i<ilen; i++){
				var oMe = $(els[i]);
				if(oMe.attr('name')){
					strArguments += '&' + oMe.attr('name') + '=' + els[i].value;
				}
			}
			strArguments = strArguments.replace('&','?');
			return strArguments;
		},
		
		/*封装表单元素=》json */
		form2json: function(form){
			if(!form) return;
			var jsonArguments = {};
			for(var i=0, els=form.elements, ilen=els.length; i<ilen; i++){
				var oMe = $(els[i]);
				if(oMe.attr('name')){
					jsonArguments[oMe.attr('name')] = els[i].value;
				}
			}
			return jsonArguments;
		}
	});
})(ICAT, ICAT.util, jQuery);
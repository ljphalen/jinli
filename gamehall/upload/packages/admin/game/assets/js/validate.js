var Validata = function(){
	var email = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/
		, phone = /(13[0-9]|14[57]|15[012356789]|18[012356789]|17[012356789])\d{8}/
		, number = /^[0-9]*$/
		;
	return{
		empty:function(v){
			if(!v){
				return ;
			}
			return v.replace(/(^\s*)|(\s*$)/g, "");
		},
		number:function(v){
			return number.test(v);
		},
		email:function(v){
			return email.test(v);
		},
		phone:function(v){
			return phone.test(v);
		},
		age:function(v){
		
		},
		len:function(v){
			return v.replace(/[^\x00-\xff]/g, "aa").length;
		}
	};
}();

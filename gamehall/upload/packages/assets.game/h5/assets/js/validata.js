var Validata = function(){
	var email = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/
		, number = /^[0-9]*$/
		, phone = /(13[0-9]|14[57]|15[0-9]|18[0-9]|17[0-9])\d{8}/
		;
	return{
		empty:function(v){
			return v.replace(/(^\s*)|(\s*$)/g, "");
		},
		number:function(v){
			return number.test(v);
		},
		phone:function(v){
			return phone.test(v);
		},
		email:function(v){
			return email.test(v);
		},
		len:function(v){
			return v.replace(/[^\x00-\xff]/g, "aa").length;
		}
	};
}();
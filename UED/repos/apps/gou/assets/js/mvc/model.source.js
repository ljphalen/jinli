/** GOU-model.js # */
(function(iCat){
	// Class
	GOU.Model = iCat.Model.extend({
		propTest: '公用属性',
		fnTest: function(){
			alert('公用方法');
		}
	});

	// setting
	GOU.setting = {
		// your settings...
	};
})(ICAT);
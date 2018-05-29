(function(iCat){
	Gou.model = new iCat.Model.extend({
		fnTest: function(){
			alert(0);
		}
	});

	Gou.model.setting = {
		'mainModel': {
			staticAttr: 'xxx',
			fun: function(){ alert('yyy'); }
		}
	};
})(ICAT);
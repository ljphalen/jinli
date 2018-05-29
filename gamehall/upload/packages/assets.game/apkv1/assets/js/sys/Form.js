var Form = function(id){
	this.id = id;
};
Form.prototype.getVal = function(){
	var vals = {},name,val
	;
	$(this.id).find(':text,:hidden').each(function(){
		name = $(this).attr('name');
		val = $(this).val();
		if(!name)
			return;
		vals[name] = val;
	});
	return vals;
};
Form.prototype.getVals = function(){
	var vals = {},name,val
	;
	$(this.id).find('input').each(function(){
		name = $(this).attr('name');
		val = $(this).val();
		if(!name)
			return;
		vals[name] = val;
	});
	$(this.id).find('textarea').each(function(){
		name = $(this).attr('name');
		val = $(this).val();
		if(!name)
			return;
		vals[name] = val;
	});
	return vals;
};
Form.prototype.getArrayVal = function(config){
	var json = {}
		,$id = $(this.id)
		,length = config.length
	;
	for(var i=0; i<length; i++){
		json[config[i]] = $id.find('[name="'+config[i]+'"]').val();
	}
	return json;
}
Form.prototype.setHtml = function(data,filter){
	filter = filter ||[''];
	var length = filter.length;
	for(var name in data){
		for(var i=0;i<length;i++){
			if(filter[i] != name){
				$(this.id).find('[name="'+name+'"]').html(data[name]);
			}
		}
	}
};
Form.prototype.setVal = function(data,filter){
	filter = filter ||[''];
	var length = filter.length;
	console.log(JSON.stringify(data));
	for(var name in data){
		for(var i=0;i<length;i++){
			if(filter[i] != name){
				$(this.id).find('[name="'+name+'"]').val(data[name]);
			}
		}
	}
};
Form.prototype.close = function(config){
	$(this.id).find(':text').each(function(){
		$(this).val('');
	});
	$(this.id).find('select option:eq(0)').prop('selected','selected');
};
!function(Class){
	var Model = function( config ){
		$.extend( this,config );
		this.data = this.data || {};
	}
	Model.prototype.send = function( config,errorCalle){
		
		var url = config.url || this.url
			,succ = config.succ || this.succ
			,loadName = this.loadName
		;
		if(loadName)
			$(loadName).show();
		$.ajax({
			url: url,
			method: 'get',
			data: config.data,
			success:function ( data ){
				if(loadName)
					$(loadName).hide();
				data = JSON.parse(data);
				if(succ)
					succ(data);
			}
			,error:function(e){
				if(loadName)
					$(loadName).hide();
				if(errorCalle){
					errorCalle(e);
					return;
				}
			}
		});
	};
	
	Model.prototype.save = function( succ,errorCalle ){
		var parse = this
			,loadName = this.loadName
		;
		if(loadName)
			$(loadName).show();
		$.ajax({
			url: this.url,
			method: 'POST',
			data: this.data,
			success:function ( data ){
				if(loadName)
					$(loadName).hide();
				data = JSON.parse(data);
				if( succ )
					succ(data);	
				
				if( parse.location)
					location.href = parse.location;
			}
			,error:function(e){
				if(loadName)
					$(loadName).hide();
				if(errorCalle){
					errorCalle(e);
					return;
				}
			}
		});
	};
	
	Model.prototype.del = function(id,succ,errorCalle){
		var loadName = this.loadName
		;
		if(!confirm('您确定要删除吗?')){
			return;
		}
		if(loadName)
			$(loadName).show();
		$.ajax({
			url:this.url,
			method: "POST",
			data: {id:id},
			success:function ( data )
			{
				if(loadName)
					$(loadName).hide();
				data = JSON.parse(data);
				if(succ)
					succ(data);
			}
			,error:function(e){
				if(loadName)
					$(loadName).hide();
				if(errorCalle){
					errorCalle(e);
					return;
				}
			}
		});
	}
	Model.prototype.lock = false;
	Class.Model = Model
}(window);
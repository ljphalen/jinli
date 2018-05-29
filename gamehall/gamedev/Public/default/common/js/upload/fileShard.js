(function(Class){
	var Memory = {
		count:0
	};
	var upload = function( config ){
			var token   = Memory.tokenData
				,index      = token.shard_index_list[Memory.count]
				,start      = index * config.shardSize
				,end        = Math.min(config.file.size, start + config.shardSize)
				,fileData   = config.file.slice(start, end)
				,url        = [
				    config.uploadShardApi
					,'?upload_token='
					,token.upload_token,'&shard_index='
					,index
				].join('')
				,xhr        = new XMLHttpRequest()
			;
			
		if (! /.+\.apk$/i.test(config.file.name)) {
			if(Memory.error){
				Memory.error({
					msg: '文件格式不符合要求'
				});
			}
			return ;
		}
		
		if (config.file.size >= 2000 * 1024 * 1024) {
			if(Memory.error){
				Memory.error({
					msg: '上传文件大小不能超过2GB'
				});
			}
			return ;
		}
		
		xhr.onload = function(){
			if(Memory.count<Memory.tokenData.length){
				Memory.count++;
				upload(config);
				return;
			}
			//success
			if(Memory.success){
				Memory.success({
					upload_token: Memory.tokenData.upload_token
				});
			}
		}
		
		xhr.upload.onprogress = function(e) {
			//$('#msg').html('正在上传第'+Memory.count+'共有'+Memory.tokenData.length);
			//alert((Memory.initUploadSize + e.loaded + (Memory.count * config.shardSize)));
			//alert(Math.round((Memory.initUploadSize + e.loaded + (Memory.count * config.shardSize)) / config.file.size * 100));
			if(Memory.loading){
				Memory.loading({
					percent: Math.round((Memory.initUploadSize + e.loaded + (Memory.count * config.shardSize)) / config.file.size * 100)
				});
			}
		};
		
		xhr.open('POST', url, true);
		xhr.send(fileData);

	};
	
	var FileShard = function(config){
		this.shardSize   = 10 * 1024 * 1024;
		Memory.loading = config.loading;
		Memory.success = config.success;
		Memory.error = config.error;
		$.extend(this,config);
	};
	FileShard.prototype.send = function(){
		parent = this;
		if( !this.file ){
			alert('请选择要上传的文件!');
			return;
		}
		$.get(this.getUploadTokenApi,{
				name:this.file.name,
				total_size:this.file.size,
				shard_size:this.shardSize,
				last_modified:this.file.lastModified
			},function(data){
				data  = JSON.parse(data);
				Memory.tokenData        = data.data;
				Memory.tokenData.length = Memory.tokenData.shard_index_list.length;
				Memory.initUploadSize = (Math.ceil(parent.file.size / parent.shardSize) - Memory.tokenData.shard_index_list.length) * parent.shardSize;
				if(!Memory.tokenData.length){
					//success
					if(Memory.success){
						Memory.success({
							upload_token: Memory.tokenData.upload_token
						});
					}
					return;
				}
				Memory.tokenData.length--;
				upload({
					file:parent.file
					,shardSize:parent.shardSize
					,uploadShardApi: parent.uploadShardApi
				});
			});
	};
	
	Class.FileShard = FileShard;
}(window));
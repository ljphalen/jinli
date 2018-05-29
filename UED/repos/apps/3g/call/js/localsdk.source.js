var localSDK = {
	firstId: null,
	secondId: null,
	localapi: function(){
		return window.ChangliaoJSInterface ? window.ChangliaoJSInterface : null;
	},
	getperm: function(){
		if(localSDK.support().code == 1){
			var contact = localSDK.contact();

			localSDK.timeId = setTimeout(function(){
				localSDK.getperm();
			},500);

			if(contact.code == 1){
				clearTimeout(localSDK.timeId);
			}

			if(contact.code == 3){
				var listWrap = document.getElementById('js-contcat-list');
				var list = contact.list;
				var arr = [];
				if(list){
					for(var i = 0, lens = list.length; i < lens; i++){
						arr.push(
							'<li class="ui-border-b">\
								<a href="<?php echo $webroot;?>/front/activity/cindex/?tel='+list[i]['number']+'&name='+list[i]['name']+'">\
									<p class="call-name">'+(list[i]['name'] || '(未知)')+'</p>\
									<p class="call-tel">'+list[i]['number']+'</p>\
								</a>\
							</li>'
						);
					}
				}
				listWrap.innerHTML = arr.join('');
				clearTimeout(localSDK.timeId);
			}
		}
	},
	json2obj: function(str){
		return JSON.parse(str);
	},
    network: function(){
    	if(localSDK.localapi()){
    		var data = localSDK.json2obj(localSDK.localapi().getNetworkState());

            if(data.result == 0){
                return {"code": data.result, "text": "无网络"};
            }

            if(data.result == 1){
                return {"code": data.result, "text": "wifi"};
            }

            if(data.result == 2){
                return {"code": data.result, "text": "移动网络"};
            }
    	}

    	return {"code": 0, "text": "无网络"};
    },
    contact: function(){
        if(localSDK.localapi()){
        	var data = localSDK.json2obj(localSDK.localapi().getContacts());

            if(data.data == 'getcontacts' && data.result == 0){
            	// 获取权限成功
            	return {"code": 0, "text": "获取权限成功"};
            }

            if(data.data == 'getcontacts' && data.result == 1){
            	// 获取权限拒绝
            	return {"code": 1, "text": "获取权限拒绝"};
            }

            if(data.data == 'getcontacts' && data.result == 2){
            	// 获取权限等待
            	return {"code": 2, "text": "获取权限等待"};
            }

            if(data.list){
            	// 获取权限等待
            	return {"code": 3, "list": data.list, "text": "获取联系人成功"};
            }
        }
    },
    support: function(){
    	if(localSDK.localapi()){
    		var data = localSDK.json2obj(localSDK.localapi().hasSdk());

            if(data.data === 'hassdk' && data.result == 1){
                return {"code": 1, "text": "支持"}
            } else {
            	return {"code": 0, "text": "不支持"}
            }
    	}

    	return {"code": 0, "text": "不支持"}
    },
    tel: function(str){        	
        if(localSDK.localapi()){
            var data = localSDK.json2obj(localSDK.localapi().dial(str));

            if(data.result == 1){
                return {"code": 1, "text": "成功"};
            } else {
                return {"code": 0, "text": "失败"};
            }
    	}
    }
};
// JavaScript Document
function getFileName(url)
{
	var urlArr=url.split('/');
	return urlArr[urlArr.length-1];
}
function changeSort(obj, act, id)
{
	var tag = obj.firstChild.tagName;

  	if( typeof(tag) != "undefined" && tag.toLowerCase() == "input" )
  	{
    	return;
  	}

  	/* 保存原始的内容 */
 	var org = obj.innerHTML;
  	var val = obj.innerHTML;//Browser.isIE ? obj.innerText : obj.textContent;

  	/* 创建一个输入框 */
  	var txt = document.createElement("INPUT");
  	txt.value = (val == 'N/A') ? '' : val;
  	txt.style.width = (obj.offsetWidth + 12) + "px" ;

  	/* 隐藏对象中的内容，并将输入框加入到对象中 */
  	obj.innerHTML = "";
  	obj.appendChild(txt);
  	txt.focus();
	/* 编辑区失去焦点的处理函数 */
  	txt.onblur = function()
	{
		if(this.value==org){
			obj.innerHTML = org;
		}
		else{
			$.ajax({
		   		type: "POST",
		   		url: act,
		   		data: "id="+id+"&val="+txt.value,
		   		success: function(msg){
		    		if(msg=='ok' ){
		    			obj.innerHTML = txt.value;
		    		}
		    		else{
		    			alert(msg);
						obj.innerHTML = org;
		    		}
		   		}
			});
		}
	}
}
function changeState(obj,act,id,val)
{
	$.ajax({
		   type: "POST",
		   url: act,
		   data: "id="+id+"&val="+val,
		   success: function(msg){
		    if(msg=='ok' ){	
		    	if( getFileName(obj.src)=='no.gif' ){
		    		obj.src='/template/default/img/yes.gif';
		    	}
		    	else{
		    		obj.src='/template/default/img/no.gif';
		    	}
		    }
		    else{
		    	alert(msg);
		    }
		   }
	}); 	
}
function delPrompt()
{
	if( confirm('您真的要删除吗？不可恢复') ){
		return true;
	}
	else{
		return false;
	}
}
function checkAccount(curObj,defaultVal)
{
	if(curObj.value==defaultVal){
		return;
	}
	else
	{
		$.ajax({
			   type: "POST",
			   url: "/cms.php/adminManage/checkAccount",
			   data: "val="+curObj.value,
			   success: function(msg)
			   {
				   if(msg=='ok' )
				   {	
						return;
				   }
				   else{
					   	alert('你输入的帐号重复，请您再输入一个帐号');
						curObj.value='';
				   }
			  }
		});	
	}
}
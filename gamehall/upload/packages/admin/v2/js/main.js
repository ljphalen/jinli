//查询
function search(datagrid,formId){
	datagrid.datagrid('load',$.serializeObject($(formId).form()));
}

//导出数据到csv文件
function exportExcel(formId){
	url = $(formId).attr("action");
	newUrl = setUrl(url,'ex',1);
	$(formId).attr('action',newUrl).attr('method','post').attr('target','_blank').submit();
	$(formId).attr('action',url);
}


//删除数据
function delData(datagrid,actUrl,field){
	postData(actUrl,true,'数据删除后无法恢复，确定要删除数据吗？',datagrid,true,field);
}

//新增数据
function addData(title,formId,datagrid,actUrl,width,height,maximized){
	return editDialog(title,formId,datagrid,setUrl(actUrl),width,height,maximized);
}
//编辑数据
function editData(rowIndex,rowData,title,formId,datagrid,actUrl,width,height,field,maximized){
	//console.log(rowData);
	if(field==undefined || field=="") field="id";
    if(rowIndex != undefined) {
    	fieldValue = eval("rowData."+field);
    	return editDialog(title,formId,datagrid,setUrl(actUrl,field,fieldValue),width,height,maximized);        
    }
    //选中的所有行
	var rows = datagrid.datagrid('getSelections');
	//选中的行（第一次选择的行）
	var row = datagrid.datagrid('getSelected');
	if (row){
		if(rows.length>1){
			//row = rows[rows.length-1];
			$.messager.alert("提示信息","不能选择多个对象进行操作！");
		}else{
            fieldValue = eval("row."+field);
            return editDialog(title,formId,datagrid,setUrl(actUrl,field,fieldValue),width,height,maximized);
        }
	}else{
		$.messager.alert("提示信息","请选择要操作的对象！");
	}
}


//提交数据
function postData(actUrl,isShowMsg,confirmText,datagrid,isGetRow,field){	
	if(field==undefined || field=="") field="id";	
	var rows = 1;
	var params = '';
	if(datagrid!=undefined ){
        if(isGetRow==true){
            rows = datagrid.datagrid('getSelections');
            var ids = new Object();
            if(rows.length > 1){
                for(var i=0;i<rows.length;i++){
                    ids[i] = eval("rows[i]."+field);
                }
            }else{
                ids = eval("rows[0]."+field);
            }
            //params = eval("{'" + field + "':ids}");
            params = {id:ids,'token':token};
        }
		if(rows.length > 0){	
			if(confirmText == undefined || confirmText==''){
				post(actUrl,isShowMsg,datagrid,params);
			}else{
				$.messager.confirm('确认提示！',confirmText,function(r){
					if(r){
						post(actUrl,isShowMsg,datagrid,params);
					}
				});
			}
		}else{
			$.messager.alert('提示信息',"请选择要操作的对象！");
		}
	}
}

function post(actUrl,isShowMsg,datagrid,params){
	if(params==undefined) params = '';
	$.post(actUrl,params,function(data){
		if (data.success === true){			
			if(isShowMsg==undefined || isShowMsg) $.messager.alert('提示信息',textDecode(textDecode(data.msg)));
			if(datagrid!=undefined) datagrid.datagrid('load');	// reload the user data
		} else {
			$.messager.alert('错误信息',textDecode(textDecode(data.msg)));
		}
	},'json');	
}


//上传文件对话框
//必选：产品KEY，文件类型，
//可选：thumb-缩略图，isMore-[0|1]是否可多文件上传，isAdd-上传结果是否追加到textId上,textId-接收结果ID，datagrid-列表ID,splitStr-多文件时分分隔符
function upfileDialog(fileType,thumb,memo,isMore,isAdd,textId,splitStr,datagrid){
	if(fileType==undefined || fileType=='') {$.messager.alert('提示','参数错误：上传文件类型未指定！');return;	}
	if(textId==undefined) textId='';
	if(thumb==undefined) thumb='';	
	if(memo==undefined) memo='';
	if(datagrid==undefined || datagrid=='') datagrid=null;
	if(splitStr==undefined || splitStr=='') splitStr='';
	isAdd = isAdd ? true : false;
	isMore = isMore ? 1 : 0;	
	var the_form;
	var the_dialog;		
	the_dialog = $("<div/>").dialog({
		title: 	'上传文件',
		width: 	500,
		height: 500,
		modal: true,
		href: 	'/Admin/upfile/index?fileType='+fileType+'&thumb='+thumb+'&isMore='+isMore+'&memo='+memo,
		buttons: [{
			text: '确定',
			handler: function(){the_form.submit();},
		},{
			text: '关闭',
			handler: function(){
				the_dialog.dialog('destroy');
			},
		}],
		onClose : function() {
			$(this).dialog('destroy');
		},
		onLoad : function(data){
			
			if(isJson(data)){
				var json = $.parseJSON(data);
				$.messager.alert('提示信息',textDecode(json.info));
				the_dialog.dialog('destroy');//销毁对话框 
				return;
			}		
			
			//初始化表单
			the_form = $('#upfile_form').form({
				url: '/Admin/Upfile/upfile',
				onSubmit: function(param){
					$.messager.progress({ title : '提示信息', text : '数据处理中，请稍候....'	});
			    },
				success: function(data){
					$.messager.progress('close');
					var json = $.parseJSON(data); 
					if (json.status == 1){
						if(datagrid!=null) datagrid.datagrid('reload');	// reload the user data
						if(textId!='') {
							var files = json.data.files;
							if(isMore==1 && splitStr!="") {files = files.replace(/,/g,splitStr); }
							if(isAdd==true){
								$val = $(textId).val();
								$val = $val + ($val!="" ? splitStr : "") + files;
								$(textId).val($val);
							}else{
								$(textId).val(json.data.files);
							}
							
						}
						$.messager.alert('提示信息',textDecode(json.info));//操作结果提示						
					}else {
						$.messager.alert('错误信息',textDecode(json.info));//操作结果提示
					}
					the_dialog.dialog('destroy');//销毁对话框 
				}
			});	
		},
	}).dialog('open');
}

//编辑对话窗口
function editDialog(title,formId,datagrid,actUrl,width,height,maximized){
	if(datagrid==undefined || datagrid=='') datagrid==null;
	if(width==undefined) width=400;
	if(height==undefined) width=300;
	if(formId==undefined || formId==null) formId="";
	var the_form = null;
	var the_dialog;	
	the_dialog = $("<div/>").dialog({
		title: title,
		width: width,
		height: height,
		href: actUrl,
		modal: true,
		maximizable: true,
		maximized: (maximized != undefined ? maximized : false),
		buttons : [{
			text : '确定',
			iconCls : 'icon-save',
			handler : function() {
				if(the_form==null){
					the_dialog.dialog('destroy');
				}else{
					the_form.submit();
				}
			}
		},{
			text : '关闭',
			iconCls : 'icon-cancel',
			handler : function() {
				the_dialog.dialog('destroy');
			}
		}],		
		onClose : function() {
			$(this).dialog('destroy');
		},
		onLoad : function(data){
			//判断是否返回JSON格式数据（主要用于权限处理）
			if(isJson(data)){
				var json = $.parseJSON(data);
				$.messager.alert('提示信息',textDecode(json.msg));
				the_dialog.dialog('destroy');//销毁对话框 
				return;
			}
			if(formId=="") return;
			$(formId).find("#submit").hide();
			//初始化表单
			the_form = $(formId).form({
				url: actUrl,
				onSubmit: function(param){ 
					$.messager.progress({ title : '提示信息', text : '数据处理中，请稍候....'	});
			    },
				success: function(data){
					$.messager.progress('close');
					var json = $.parseJSON(data); 					
					if (json.success == true){							
						if(datagrid!=null) datagrid.datagrid('reload');	// reload the user data
						$.messager.alert('提示信息',textDecode(json.msg));//操作结果提示
						the_dialog.dialog('destroy');//销毁对话框 
					}else {
						$.messager.alert('错误信息',textDecode(json.msg));//操作结果提示
					}					
				},
			});
			
		},
	}).dialog('open');
	return the_dialog;
}

//弹出查看表头列选择对话窗
function selColDialog(datagridId){
	var columns = $(datagridId).datagrid('getColumnFields');	
	var content = '';
	for(i=0; i<columns.length; i++){		
		opt = $(datagridId).datagrid('getColumnOption',columns[i]);		
		content = content + '<div><input type="checkbox" id="ck' + opt.field + '" onclick="selCol(this,\'' + datagrid + '\',\'' + opt.field + '\')"' + (opt.hidden ? '' : 'checked="checked"') + '><label for="ck' + opt.field + '">' + opt.title + '</label></div>';
	}
	content = '<div id="selColDialog" style="padding:10px 40px 20px 10px;">' + content + '</div>';
	the_dialog = $('<div/>').dialog({  
	    content: content,
	    title: '查看列',
	    minimizable: false,
	    maximizable: false,
		buttons : [{
			text : '关闭',
			iconCls : 'icon-cancel',
			handler : function() {
				the_dialog.dialog('destroy');
			}
		}],		
		onClose : function() {
			the_dialog.dialog('destroy');
		},
	}).dialog('open');
}
//显示/隐藏表头列
function selCol(obj,datagridId,id){
	if($(obj).attr("checked")=="checked"){
		$(datagridId).datagrid('showColumn',id); 
	}else{
		$(datagridId).datagrid('hideColumn',id); 
	}
}

//更改URL参数
function setUrl(url, ref, value) {
	if(url==undefined) url='';
    var str = "";
    if (url.indexOf('?') != -1)
        str = url.substr(url.indexOf('?') + 1);
    else
        return url + "?" + ref + "=" + value + '&_skip=1';
    var returnurl = "";
    var setparam = "";
    var arr;
    var modify = "0";
    if (str.indexOf('&') != -1) {
        arr = str.split('&');
        for (i in arr) {
            if (arr[i].split('=')[0] == ref) {
                setparam = value;
                modify = "1";
            }
            else {
                setparam = arr[i].split('=')[1];
            }
            returnurl = returnurl + arr[i].split('=')[0] + "=" + setparam + "&";
        }
        returnurl = returnurl.substr(0, returnurl.length - 1);
        if (modify == "0")
            if (returnurl == str)
                returnurl = returnurl + "&" + ref + "=" + value;
    }
    else {
        if (str.indexOf('=') != -1) {
            arr = str.split('=');
            if (arr[0] == ref) {
                setparam = value;
                modify = "1";
            }
            else {
                setparam = arr[1];
            }
            returnurl = arr[0] + "=" + setparam;
            if (modify == "0")
                if (returnurl == str)
                    returnurl = returnurl + "&" + ref + "=" + value;
        }
        else
            returnurl = ref + "=" + value;
    }
    return url.substr(0, url.indexOf('?')) + "?" + returnurl + '&_skip=1';
}

//格式化PHP时间戳
function dateFormat(timestamp)
{
	
    update=new Date(timestamp*1000);//时间戳要乘1000
    year=update.getFullYear();
    month=(update.getMonth()+1<10)?('0'+(update.getMonth()+1)):(update.getMonth()+1);
    day=(update.getDate()<10)?('0'+update.getDate()):(update.getDate());
 
    hour=(update.getHours()<10)?('0'+update.getHours()):(update.getHours());
    minute=(update.getMinutes()<10)?('0'+update.getMinutes()):(update.getMinutes());
    second=(update.getSeconds()<10)?('0'+update.getSeconds()):(update.getSeconds());
 
    return (year+'-'+month+'-'+day+' '+hour+':'+minute+':'+second);
}
function textEncode(str) {
	 str = str.replace(/&amp;/gi, '&');
	 str = str.replace(/</g, '&lt;');
	 str = str.replace(/>/g, '&gt;');
	 return str;
}

function textDecode(str) {
	 str = str.replace(/&amp;/gi, '&');
	 str = str.replace(/&lt;/gi, '<');
	 str = str.replace(/&gt;/gi, '>');
	 return str;
	}

function isJson(str){	
	try{
		var json = $.parseJSON(str);
		return true;
	}catch(e){
		return false;
	}
}

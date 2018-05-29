/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var BASE_URL = "http://backend.mobgi.com";
var TEMPLATE_PATH = '/misc/ledou';
var MISC_BASEURL = BASE_URL + TEMPLATE_PATH;

$(function() {
	//点击出现图片选择
	$(".pickimg").click(function(){
	    var wind = window.open("/resource/material_pic?ad_subtype=10","newwindow","toolbar=no,menubar=no,resizable=no,location=no,status=no");
	 })
    // 删除按钮 全局搞定
    $(".del").click(function() {
        var opt = $(this);
        var url = opt.attr("href");
        opt.attr("href", "#");
        jConfirm("是否确定要删除？", function() {
            window.location.href = url;
        }, function() {
            opt.attr("href", url);
        });
    });
    // 无样式删除按钮 全局搞定
    $(".btndel").click(function() {
        var opt = $(this);
        var url = opt.attr("href");
        opt.attr("href", "#");
        jConfirm("是否确定要删除？", function() {
            window.location.href = url;
        }, function() {
            opt.attr("href", url);
        });
    });
    //--------------------左右选择框---------------------------
    // 双击事情
    $(".multi > option").live("dblclick", function() {
        var sel = $(this).parent('select').attr('_target');
        $(this).remove();
        $("#" + sel).append($(this).removeAttr("selected"));
    });
    // 添加，删除事件
    $(".selMove").live("click", function() {
        var fromId = $(this).attr("_from");
        var toId = $(this).attr("_to");
        var fromOpt = $("#" + fromId + " > option:selected");
        fromOpt.remove();
        $("#" + toId).append(fromOpt);
    });

    // Input搜索框
    $(".search_input").live("keyup", function() {
        var sel_val = $(this).val();
        var sel_id = $(this).attr("id");
        var searchRsOpt = $("#" + sel_id + "_sel").children("option[_name*='" + sel_val + "']");
        searchRsOpt.remove();
        $("#" + sel_id + "_sel").prepend(searchRsOpt);
    });
    //--------------------左右选择框end---------------------------
    // 全局搞定Form数据验证
    $("form").validate();
    // 表格效果
    $(".stat_tbl > tbody > tr").mouseover(function() {
        $(this).css("background", "#61D5FF");
    }).mouseout(function() {
        $(this).css("background", "none");
    });

});


function ajaxPOST(url, arg, callback) {
    var callback = callback || function(data) {
        if (data.result == 0) {
            alert(data.msg);
        } else {
            alert(data.msg);
        }
    };
    $.ajax({
        type: "POST",
        url: url,
        data: arg,
        dataType: "json",
        success: callback
    })
}
function ajaxGET(url, arg, callback) {
    var callback = callback || function(data) {
        if (data.result == 0) {
            alert(data.msg);
        } else {
            alert(data.msg);
        }
    };
    $.ajax({
        type: "GET",
        url: url,
        data: arg,
        dataType: "json",
        success: callback
    })

}
// 解决广告配制列表中，TypeError: success_jsonpCallback is not a function JS问题
var success_jsonpCallback = function() {
}


function CheckUrl(str) {
    var RegUrl = new RegExp();
    RegUrl.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
    if (!RegUrl.test(str)) {
        return false;
    }
    return true;
}
function getChannelByChannel_id(channel_id, boxid) {
    var url = '/adconfig/getChannelinfo';
    ajaxGET(url, "channel_id=" + channel_id, function(data) {
        if (data.error == 0) {
            if (data.msg.length > 0) {
                var channel = '';
                for (var i = 0; i < data.msg.length; i++) {
                    if (data.msg[i] != null) {
                        channel += data.msg[i].realname + "（" + data.msg[i].identifier + "）<br>";
                    } else {
                        channel += "。。。。。。";
                    }
                }
                $("#" + boxid).html(channel)
            }
        }
    })
}
function isNumber(s) {
    var regu = "^[0-9]+$";
    var re = new RegExp(regu);
    if (s.search(re) != -1) {
        return true;
    } else {
        return false;
    }
}
function in_array(stringToSearch, arrayToSearch) {
	for (s = 0; s < arrayToSearch.length; s++) {
		thisEntry = arrayToSearch[s].toString();
		if (thisEntry == stringToSearch) {
			return true;
		}
	}
	return false;
}
function getFullPath(obj) {    //得到图片的完整路径  
    if (obj) {  
        //ie  
        if (window.navigator.userAgent.indexOf("MSIE") >= 1) {  
            obj.select();  
            return document.selection.createRange().text;  
        }  
        //firefox  
        else if (window.navigator.userAgent.indexOf("Firefox") >= 1) {  
            if (obj.files) {  
                return obj.files.item(0).getAsDataURL();  
            }  
            return obj.value;  
        }  
        return obj.value;  
    }  
}
function CheckUrl(str) { 
    var RegUrl = new RegExp(); 
    RegUrl.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");//jihua.cnblogs.com 
    if (!RegUrl.test(str)) { 
        return false; 
    } 
    return true; 
} 
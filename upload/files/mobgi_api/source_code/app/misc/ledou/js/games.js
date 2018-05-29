/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function getGameinfo(type, appkey) {
    var type = $("#" + type).val();
    var value = $("input[name='" + appkey + "']").val();

    $("#gamelist").html('');//先清空
    ajaxGET("/apps/getAppGameInfo", "type=" + type + "&value=" + value, function(data) {
        var html = '';
        if (data.length != 0) {
            for (var i = 0; i < data.length; i++) {
                html += '<li onmousedown="javascript:$(\'input[name=' + appkey + ']\').val(\'' + data[i].product_key + '\');$(\'#gamelist\').hide();">' + data[i].name + '[' + data[i].product_key + ']' + '</li>'
            }
        }
        if (html == "") {
            html = '<li onclick="CreateAppkey()">没有结果,点击自动生成一个</li>';
        }
        $("#gamelist").html(html);
        $("#gamelist").show();
    });
}
function CreateAppkey() {
    ajaxGET("/apps/createappkey", "", function(data) {
        if (data.error == 0) {
            $("#appkey").val(data.data);
            $("#gamelist").hide();
        } else {
            alert("生成APPKEY失败")
        }
    });
}
function createposkey() {
    var appkey = $("input[name='appkey']").val();
    initposbox()
    ajaxGET("/apps/createposkey", "appkey=" + appkey, function(data) {
        if (data.result == 0) {
            $("input[name='pos_key_tmp']").val(data.msg);
            $("#pos_key_text").html(data.msg)
        } else {
            alert("生成广告位KEY失败,请重试")
        }
    });
}
function initposbox() {//点击添加广告初始化
    $("input[name='pos_id_tmp']").val("")
    $("input[name='pos_key_tmp']").val("")
    $("input[name='pos_name_tmp']").val("")
    $("input[name='pos_key_type_tmp']").val("")
}
function add_pos_key() {
    var pos_id = $("input[name='pos_id_tmp']").val()
    var pos_key = $("input[name='pos_key_tmp']").val()
    var pos_name = $("input[name='pos_name_tmp']").val()
    var pos_key_type = $("select[name='pos_key_type_tmp']").val()
    var pos_key_type_text = $("select[name='pos_key_type_tmp']").find("option:selected").text();
    var trid = pos_key.substring(0, 16);
    var html = '<tr id="' + trid + '">';
    
    if (pos_key == "") {
        alert("广告位KEY不能为空");
        $("input[name='pos_key_tmp']").focus();
        return false;
    }
    if (pos_name == "") {
        alert("广告位名称不能为空");
        $("input[name='pos_name_tmp']").focus();
        return false;
    }
    if (pos_key_type == "") {
        alert("请选择广告形式");
        $("select[name='pos_key_type_tmp']").focus();
        return false;
    }
    html += '<td>' + pos_key + '<input type="hidden" name="pos_key[]" value="' + pos_key + '"/><input type="hidden" name="pos_id[]" value="' + pos_id + '"/></td>';
    html += '<td>' + pos_name + '</td>';
    html += '<td>' + pos_key_type_text + '</td>';
    html += '<td><input type="radio" name="pos_state' + trid + '[]" checked value="1"/><label>开</label><input type="radio" name="pos_state' + trid + '[]" value="0"/><label>关</label></td>';
    var acounting_method = "";
    acounting_method +='<td><select style="min-width:10%" name="acounting_method[]">';
    //插页广告默认CPM
    if(pos_key_type == 'HALF')
    {
        acounting_method += '<option value="1" selected>CPM</option>';
    }
    //推荐墙广告不可以选择“CPM”
    else if(pos_key_type =="LIST"){
        acounting_method += '<option value="1" disabled>CPM</option>';
    }else{
        acounting_method += '<option value="1">CPM</option>';
    }
    //BANNER广告，列表广告默认CPC
    if(pos_key_type == "BANNER" || pos_key_type == "LIST"){
        acounting_method += '<option value="2" selected>CPC</option>';
    }else{
        acounting_method += '<option value="2">CPC</option>';
    }
    acounting_method +='<option value="5">CPD</option>';
    acounting_method +='<option value="4">CPI</option>';
    acounting_method += '<option value="3" disabled>CPA</option>';
    acounting_method += '<option value="6" disabled>CPS</option>';
    acounting_method +='</select></td>';
    html += acounting_method;
    html += '<td><input type="text" style="width:22%" name="denominated[]" value=""/></td>';
    html += '<td><input type="hidden" name="pos_name[]" value="' + pos_name + '"/><input type="hidden" name="pos_key_type[]" value="' + pos_key_type + '"/><a onclick="update_pos_key(\'' + trid + '\')">编辑</a>  <a onclick="del_pos_key(this)">删除</a></td>';
    html += "</tr>"
    trid = $("input[name='pos_id_tmp']").val()
    if (trid != "") {//修改
        set_tr_pos_key(trid)
    } else {//新增
        $("#pos_tb_box").append(html);
    }
    createposkey()
    $("input[name='pos_name_tmp']").val("")
    $("input[name='pos_id_tmp']").val("")
    $("select[name='pos_key_type_tmp']").val("")
}
function del_pos_key(thiss) {
    if (confirm("你确定删除么?该操作不可恢复")) {
        var pos_key = $(thiss).parent().parent().find("td:eq(0)").text();
        ajaxGET("/apps/del_pos_key", "pos_key=" + pos_key, function(data) {
            if (data.result == 0) {
                $(thiss).parent().parent().remove();
                $("input[name='pos_key']").val(data.msg);
                $("#pos_key_text").html(data.msg)
            } else {
                alert("生成广告位KEY失败,请重试")
            }
        });
    }

}
function update_pos_key(trid) {
    var trobj = $("#" + trid)
    var pos_id = trobj.find("input[name='pos_id[]']").val()
    var pos_key = trobj.find("input[name='pos_key[]']").val()
    var pos_name = trobj.find("input[name='pos_name[]']").val()
    var pos_key_type = trobj.find("input[name='pos_key_type[]']").val()
    var pos_key_type_text = trobj.find("select[name='pos_key_type[]']").find("option:selected").text();
    $('#pos_box').show();
    if (pos_id == "") {
        $("input[name='pos_id_tmp']").val(trid)
    } else {
        $("input[name='pos_id_tmp']").val(pos_id)
    }
    $("input[name='pos_key_tmp']").val(pos_key)
    $("#pos_key_text").text(pos_key)
    $("input[name='pos_name_tmp']").val(pos_name)
    $("select[name='pos_key_type_tmp']").val(pos_key_type)
}
function set_tr_pos_key(trid) {
    var trobj = $("#" + trid)
    pos_key_type_text = $("select[name='pos_key_type_tmp']").find("option:selected").text()
    trobj.find("td:eq(1)").text($("input[name='pos_name_tmp']").val())
    trobj.find("input[name='pos_name[]']").text($("input[name='pos_name_tmp']").val())
    trobj.find("td:eq(2)").text(pos_key_type_text)
    trobj.find("input[name='pos_key_type[]']").val($("select[name='pos_key_type_tmp']").val())
    trobj.find("select[name='pos_key_type[]']").val($("select[name='pos_key_type_tmp']").val())
}
function set_pos_state(pos_key, state) {
    ajaxGET("/adpostion/setstate", "pos_key=" + pos_key + "&state=" + state, function(data) {
        if (data.msg == 0) {
            //alert(data.result)
        } else {
            alert(data.result)
        }
    })
}
//根据游戏名自动选择
function setgameid(gamename) {
    var result = false;
    if (gamename != "")
    {
        var options = $("#product_id").children();
        options.each(function(a, b) {
            $(b).attr("selected", false);
            if ($(b).text().replace("(T)", "").replace("(A)", "").replace("(I)", "") == gamename)
            {
                result = true;
                $(b).attr("selected", true);
                //select没有禁用select则触发select的change操作．
                if(!$("#product_id").attr("disabled")){
                    $("#product_id").change();
                }
                //重新绑定select的选择组件
                $("#product_id").removeClass("chzn-done");
                $("#product_id_chzn").remove();
                $("#product_id").chosen({});
                return false;
            }
        })
    }
    return result;
}
//判断是否已经有存在的元素
function checkExsits(rid, rtype) {
    var flag = false;
    $("#ad_title").find("tbody tr").each(function(i) {
        switch(rtype){
            case 1:
                if ($(this).find("input[name='picid[]']").val() == rid) {
                    flag = true;
                }
                break;
            case 2:
                if ($(this).find("input[name='textid[]']").val() == rid) {
                    flag = true;
                }
                break;
            case 4:
                if ($(this).find("input[name='htmlid[]']").val() == rid) {
                    flag = true;
                }
                break;
            case 5:
                if ($(this).find("input[name='videoid[]']").val() == rid) {
                    flag = true;
                }
                break;
            default:
                if ($(this).find("input[name='textid[]']").val() == rid) {
                    flag = true;
                }
                break;
        }
    })
    return flag;
}

//判断是否不存在或者只存在一个自定义方案广告
function checkMostoneCustomText(){
    var flag = true;
    var num = 0;
    var ad_type = 3;
    var rtype = 2;
    $("#ad_title").find("tbody tr").each(function(i) {
        if ($(this).find("input[name='ad_type[]']").val() == ad_type && $(this).find("input[name='rtype[]']").val()==rtype) {
            num++;
        }
    })
    if(num > 1){
        flag = false;
    }
    return flag;
}


//根据返回的素材填充表格
function append_resource(data) {
    var flag=false;
    var product_id=$("input[name='id']").val();
    var product_updated = $("input[name='id']").attr("product_updated");
    if (data.product != false) {
        $("#appkey").val(data.product["appkey"]);
//        $("input[name='purl']").val(data.product[""]);
        $("input[name='ad_product_id']").val(data.product["id"]);
    }
//    $("#ppackage").val("");
//    $("#product_version").val("");
//    $("input[name='purl']").val("");
//    $("#clickType").val(0);
//    $("#clickTypeBox").hide();
//    $("#clickType_1").hide();
//    $("#package_name").val("");
//    $("#file_md5").val("");
    if (data.apk != false) {
        //product_updated为空则表示是新增产品
        if(parseInt(data.apk['updatetime']) > parseInt(product_updated) || product_updated == ""){
            $("#ppackage").val(data.apk["package_name"]);
            $("#product_version").val(data.apk["apk_version"]);
            $("input[name='purl']").val(data.apk["apk_url"]);
            if(data.apk["apk_url"]!=""){//有值默认内建下载安装
                $("#clickType").val(1);
                $("#clickTypeBox").show();
                $("#clickType_1").show();
                packageSize();
                $("#package_name").val(data.apk["package_name"]);
                $("#file_md5").val(data.apk["package_name"]+"."+data.apk["apk_version"]);
            }
        }else{
            if($("#ppackage").val() == ""){
                $("#ppackage").val(data.apk["package_name"]);
            }
            if($("#product_version").val() == ""){
                $("#product_version").val(data.apk["apk_version"]);
            }
            if($("#input[name='purl']").val() == ""){
                $("#input[name='purl']").val(data.apk["apk_url"]);
            }
            if($("#clickType").val()==0 || $("#clickType").val()==""){
                if(data.apk["apk_url"]!=""){//有值默认内建下载安装
                $("#clickType").val(1);
                $("#clickTypeBox").show();
                $("#clickType_1").show();
                packageSize();
                $("#package_name").val(data.apk["package_name"]);
                $("#file_md5").val(data.apk["package_name"]+"."+data.apk["apk_version"]);
            }
            }
        }
    }
    if($("#ad_title_").size()!=0){//清理
        $("#ad_title_").remove();
    }
    if (data.pic != false) {
        for (var i = 0; i < data.pic.length; i++) {
            if(product_id!=data.pic[i].ad_product_id){
                alert("不属于本产品的素材,请不要添加");
                return false
            }
            var ad_type = "";
            var ad_subtype = "";
            if (data.pic[i].ad_type == 0) {
                ad_typestr = "插页广告";
            }else if (data.pic[i].ad_type == 3) {
                ad_typestr = "自定义";
            } else {
                ad_typestr = "BANNER";
            }
            if (data.pic[i].ad_subtype == 0) {
                ad_subtypestr = "-横屏"
            } else if (data.pic[i].ad_subtype == 1) {
                ad_subtypestr = "-竖屏"
            } else if (data.pic[i].ad_subtype == 2) {
                ad_subtypestr = "-自定义"
            } else {
                ad_subtypestr = '';
            }
            var str = '<tr>' +
                    '<td><input type="hidden" class="iText" name="picid[]" value="' + data.pic[i]["id"] + '"/>' + data.pic[i]["id"] + '<input type="hidden" class="iText" name="ad_type[]" value="' + data.pic[i].ad_type + '"/></td>' +
                    //'<input type="hidden" class="iText" name="ad_type[]" value="'+data.type+'"/>'+data.ad_info_id+
                    '<td>图片</td>' +
                    '<td>' + ad_typestr + ad_subtypestr + '</td>' +
                    '<td><span src='+data.pic[i].pic_url+' style="cursor:pointer;color:blue; class="pic_url"  onclick="showOriginal(this)" >' + data.pic[i]["pic_name"] + '</span></td>' +
                    '<td><input type="hidden" class="iText" name="pic_state[]" id="istate_pic_' + data.pic[i].id + '" value="1"/>' +
                    ' <span><input type="radio" checked  onclick="javascript:$(\'#istate_pic_' + data.pic[i].id + '\').val(this.value)" name="state_pic_' + data.pic[i].id + '" value="1" class="radio"><lable>on</lable></span>' +
                    '<span><input type="radio"  onclick="javascript:$(\'#istate_pic_' + data.pic[i].id + '\').val(this.value)" name="state_pic_' + data.pic[i].id + '" value="0" class="radio"><lable>off</lable></span>' +
                    '</td>' +
                    ' <td><input type="text" class="iText required" name="pic_rate[]" value="1" style="width:40px"></td>' +
                    ' <td><input type="text" class="iText required" name="screen_ratio[]" value="1.0" style="width:40px" ></td>' +
                    ' <td><input type="text" class="iText required" name="pic_show_time[]" value="10" style="width:40px"></td>' +
                    ' <td><input type="text" class="iText required" name="pic_close_wait[]" value="0" style="width:40px"></td>' +
                    '<td><input type="hidden" class="iText" name="pic_ad_target[]" value=""/>' +
                    '<input type="hidden" class="iText" name="pic_ad_click_type_object[]" value=""/><a title="自定义点击动作" class="edit clickTypeEdit" href="javascript:void(0)"></a> | <a title="删除" class="del" href="javascript:void(0)"></a>' +
                    '</td>' +
                    '</tr>';
            if (!checkExsits(data.pic[i]["id"],1)) {
                    $("#ad_title" ).find("tbody").append(str);
                    flag=true;
            }
//            if ($("#ad_title_" + data.pic[i].ad_product_id).size() == 0) {
//                var $table = $('<div id="ad_title_' + data.pic[i].ad_product_id + '" class="tablebox"><div class="gridTtitle"><span class="fl">广告素材</span><a class="btn fr" href="javascript:open_resource(\'/resource/material_pic?product_name='+data.pic[i].product_name+'\')">选择素材</a> <a class="btn fr addPUSH" href="javascript:void(0)">配置PUSH广告文案</a> <a class="btn fr averaged" href="javascript:void(0)">全部平均</a></div>' +
//                        '<table><thead><tr><th>素材ID</th><th>素材类型</th><th>广告类型</th><th>素材名称</th><th>广告状态</th><th>分配比例</th><th>屏幕显示比率</th><th>显示时长(秒)</th><th>操作</th></tr></thead><tbody></tbody></table></div>');
//                $table.find("tbody").append(str);
//                $("#adList").append($table);
//            } else {
//                if (!checkExsits(data.pic[i].ad_product_id, data.pic[i]["id"],1)) {
//                    $("#ad_title_" + data.pic[i].ad_product_id).find("tbody").append(str);
//                }else{
//                    alert("图片素材已经存在")
//                }
//            }
        }
    }
    if (data.icon != false) {
        $("#picon").val(data.icon["pic_url"]);
    }
    if (data.text != false) {
        for (var i = 0; i < data.text.length; i++) {
            if(product_id!=data.text[i].ad_product_id){
                alert("不属于本产品的素材,请不要添加");
                return false
            }
            var ad_type = "";
            var ad_subtype = "";
            if (data.text[i].subtype == 1) {
                ad_typestr = "BANNER";
            } else if(data.text[i].subtype == 2) {
                ad_typestr = "PUSH";
            } else if(data.text[i].subtype == 3) {
//                if(!checkExsitsCustom(data.text[i].subtype)){continue;}
                ad_typestr = "自定义";
            }
            var str = '<tr>' +
                    '<td><input type="hidden" class="iText" name="textid[]" value="' + data.text[i]["id"] + '"/>' + data.text[i]["id"] + '<input type="hidden" class="iText" name="ad_type[]" value="'+data.text[i].subtype+'"/><input type="hidden" class="iText" name="rtype[]" value="2"/></td>' +
                    //'<input type="hidden" class="iText" name="ad_info_id[]" value="'+data.ad_info_id+'"/>'+
                    '<td>文案</td>' +
                    '<td>' + ad_typestr + '</td>' +
                    '<td>' + '<span style="cursor:pointer;color:blue;" class="pic_url"  onclick="showTextContent('+"'"+data.text[i].content+"'"+ ')"  content="'+data.text[i].content+'" title="'+data.text[i].content+'">'+ data.text[i]["product_name"] + "-" + data.text[i]["type"] + "-" + data.text[i].createtime+'</span>'+ '</td>' +
                    '<td><input type="hidden" class="iText" name="text_state[]" id="istate_text_' + data.text[i].id + '" value="1"/>' +
                    ' <span><input type="radio" checked onclick="javascript:$(\'#istate_text_' + data.text[i].id + '\').val(this.value)" name="state_text_' + data.text[i].id + '" value="1" class="radio"><lable>on</lable></span>' +
                    '<span><input type="radio"  onclick="javascript:$(\'#istate_text_' + data.text[i].id + '\').val(this.value)" name="state_text_' + data.text[i].id + '" value="0" class="radio"><lable>off</lable></span>' +
                    '</td>' +
                    ' <td><input type="text" class="iText required" name="text_rate[]" value="1" style="width:40px"></td>' +
                    ' <td></td>' +
                    ' <td></td>' +
                    ' <td></td>' +
                    '<td><input type="hidden" class="iText" name="text_ad_target[]" value=""/>' +
                    '<input type="hidden" class="iText" name="text_ad_click_type_object[]" value=""/><a title="自定义点击动作" class="edit clickTypeEdit" href="javascript:void(0)"></a> | <a title="删除" class="del" href="javascript:void(0)"></a>' +
                    '</td>' +
                    '</tr>';
            if (!checkExsits(data.text[i]["id"], 2)) {
                $("#ad_title").find("tbody").append(str);
                flag=true;
            }
            
//            if ($("#ad_title_" + data.text[i].ad_product_id).size() == 0) {
//                var $table = $('<div id="ad_title_' + data.text[i].ad_product_id + '" class="tablebox"><div class="gridTtitle"><span class="fl">广告素材</span><a class="btn fr averaged" href="javascript:open_resource(\'/resource/material_pic?product_name='+data.text[i].product_name+'\')">选择素材</a> <a class="btn fr addPUSH" href="javascript:showPopUpPUSH();">配置PUSH广告文案</a> <a class="btn fr averaged" href="javascript:void(0)">全部平均</a></div>' +
//                        '<table><thead><tr><th>素材ID</th><th>素材类型</th><th>广告类型</th><th>素材名称</th><th>广告状态</th><th>分配比例</th><th>屏幕显示比率</th><th>显示时长(秒)</th><th>操作</th></tr></thead><tbody></tbody></table></div>');
//                $table.find("tbody").append(str);
//                $("#adList").append($table);
//            } else {
//                if (!checkExsits(data.text[i].ad_product_id, data.text[i]["id"], 2)) {
//                    $("#ad_title_" + data.text[i].ad_product_id).find("tbody").append(str);
//                }else{
//                    alert("文案素材已经存在")
//                }
//            }
        }
    }
    if (data.html != false) {
        for (var i = 0; i < data.html.length; i++) {
            if(product_id!=data.html[i].ad_product_id){
                alert("不属于本产品的素材,请不要添加");
                return false
            }
            var ad_type = "";
            var ad_subtype = "";
            var close_html="";
            if (data.html[i].ad_type == 0) {
                ad_typestr = "插页广告-网页";
                close_html=' <td><input type="text" class="iText required" name="html_close_wait[]" value="0" style="width:40px"></td>';
            } else if(data.html[i].ad_type == 1) {
                ad_typestr = "BANNER";
            } else{
//                if(!checkExsitsCustom(data.html[i].subtype)){continue;}
                ad_typestr = "未知";
            }
            var str = '<tr>' +
                    '<td><input type="hidden" class="iText" name="htmlid[]" value="' + data.html[i]["id"] + '"/>' + data.html[i]["id"] + '<input type="hidden" class="iText" name="ad_type[]" value="'+data.html[i].ad_type+'"/><input type="hidden" class="iText" name="rtype[]" value="4"/></td>' +
                    //'<input type="hidden" class="iText" name="ad_info_id[]" value="'+data.ad_info_id+'"/>'+
                    '<td>网页</td>' +
                    '<td>' + ad_typestr + '</td>' +
                    '<td>' + '<span style="cursor:pointer;color:blue;" class="pic_url"  onclick="javascript:window.open\''+data.html[i].html_url+'\')">'+ data.html[i]["html_name"]+'</span>'+ '</td>' +
                    '<td><input type="hidden" class="iText" name="html_state[]" id="istate_html_' + data.html[i].id + '" value="1"/>' +
                    ' <span><input type="radio" checked onclick="javascript:$(\'#istate_html_' + data.html[i].id + '\').val(this.value)" name="state_html_' + data.html[i].id + '" value="1" class="radio"><lable>on</lable></span>' +
                    '<span><input type="radio"  onclick="javascript:$(\'#istate_html_' + data.html[i].id + '\').val(this.value)" name="state_text_' + data.html[i].id + '" value="0" class="radio"><lable>off</lable></span>' +
                    '</td>' +
                    ' <td><input type="text" class="iText required" name="html_rate[]" value="1" style="width:40px"></td>' +
                    ' <td><input type="text" class="iText required" name="screen_ratio[]" value="1.0" style="width:40px" ></td>' +
                    ' <td><input type="text" class="iText required" name="html_show_time[]" value="10" style="width:40px"></td>' +close_html+
                    '<td>' +
                    '<input type="hidden" class="iText" name="html_ad_click_type_object[]" value=""/><a title="自定义点击动作" class="edit clickTypeEdit" href="javascript:void(0)"></a> | <a title="删除" class="del" href="javascript:void(0)"></a>' +
                    '</td>' +
                    '</tr>';
            if (!checkExsits(data.html[i]["id"], 4)) {
                $("#ad_title").find("tbody").append(str);
                flag=true;
            }
        }
    }
    if (data.video != false) {
        for (var i = 0; i < data.video.length; i++) {
            if(product_id!=data.video[i].ad_product_id){
                alert("不属于本产品的素材,请不要添加");
                return false
            }   
            var str = '<tr>' +
                    '<td><input type="hidden" class="iText" name="incentive_video_ad_id[]" value="" />' + 
                    '<input type="hidden" class="iText" name="incentive_video_id[]" value="' + data.video[i]["id"] + '"/>' + data.video[i]["id"] +'</td>' +
                    '<td>视频广告</td>' +
                   '<td>' +
          		   '<input type="hidden" class="iText" name="incentive_video_name[]" value="' + data.video[i]["video_name"] + '" />' +data.video[i]["video_name"]+
                   '</td>'+
                    '<td><input type="hidden" class="iText" name="incentive_video_state[]"  id="video_istate_pic_' + data.video[i].id + '" value="1"/>' +
                    ' <span><input type="radio" checked onclick="javascript:$(\'#video_istate_pic_' + data.video[i].id + '\').val(this.value)" name="incentive_video_state_radio_' + data.video[i].id + '" value="1" class="radio"><lable>on</lable></span>' +
                    '<span><input type="radio"  onclick="javascript:$(\'#video_istate_pic_' + data.video[i].id + '\').val(this.value)" name="incentive_video_state_radio_' + data.video[i].id + '" value="0" class="radio"><lable>off</lable></span>' +
                    '</td>' +
                    ' <td><input type="text" class="iText required" name="incentive_video_rate[]" value="1" style="width:40px"></td>' +
                    '<td>'+
                    ' <a title="删除" class="del" href="javascript:void(0)"></a>' +
                    '</td>' +
                    '</tr>';
            if (!checkVedioExsits(data.video[i]["id"], 6)) {
                $("#ad_title_video").find("tbody").append(str);
                flag=true;
            }

        }
    }
    if(!flag){
        jAlert("最近无更新素材");
    }
}

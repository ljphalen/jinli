//产品目标点击类型
        var adparam = {};
        function initadparamad(_v,adtarget,adclicktype){
            var _v = _v || $("#adclickType").val();
            if(_v == 2){
                adparam = {};
                adparam.start_app = {};
            }
            if(_v == 3){
                adparam = {};
                adparam.direct_url = {};
            }
            if(_v == 4){
                adparam = {};
                adparam.inner_url = {};
            }
            if(_v == 5){
                adparam = {};
                adparam.ad_list = {};
            }
            if(_v == 6){
                adparam = {};
                adparam.user_action = {};
            }
             $.each($("#adclickType_" + _v).find("input"),function(){
                if($(this).hasClass("submit")){
                    return true;//跳过“自动获取”按钮的当次循环
                }
                 if(_v == 2){
                     adparam.start_app[$(this).attr("name")] = $(this).val();
                 }
                 if(_v == 3){
                     adparam.direct_url[$(this).attr("name")] = $(this).val();
                 }
                 if(_v == 4){
                     adparam.inner_url[$(this).attr("name")] = $(this).val();
                 }
                 if(_v == 5){
                     adparam.ad_list[$(this).attr("name")] = $(this).val();
                 }
                 if(_v == 6){
                     adparam.user_action[$(this).attr("name")] = $(this).val();
                 }
            });
            if(_v == 100){
                adclicktype.val("{\"no_action\":\"\"}").change();
            }else if(_v==1){
                adclicktype.val('').change();
                $("#")
             }else{
                adclicktype.val(obj2str(adparam)).change();
            }
            adtarget.val($("#ad_target").val());
        }
        function selectAdClickType(adtarget,adclicktype){
            var _v = $("#adclickType").val();
            if(_v==1 || _v==100){
                $("#ad_target_box").hide();
            }else{
                $("#ad_target_box").show();
            }
            if(_v==2 || _v==6){
                $("#adclickTypeBox").show();
                $("#adclickType_" + _v).show().siblings().hide();
            }else{
                $("#adclickTypeBox").hide();
            }
            //iniadClickType(adclicktype)
            initadparamad(_v,adtarget,adclicktype);
            //console.log(adparam)
        }
        //将json数据填充input
        function iniadClickType(adclicktype){
            var clickobj=adclicktype.val();
            if(clickobj!=''){
                if(adclicktype!=100){
                    $("#ad_target_box").show();
                }
                $("#adclickTypeBox").show();
                $("#adclickType_1").hide();
                clickobj=str2json(clickobj);
                
                if(typeof clickobj.start_app!="undefined"){
                    $("#adclickType").val(2);
                    $("#adclickType_2").show();
                    $("#adpackage_name_start_app").val(clickobj.start_app.package_name);
                    $("#adpactivity_start_app").val(clickobj.start_app.activity);
                }
                if(typeof clickobj.direct_url!="undefined"){
                    $("#adclickType").val(3);
                }
                if(typeof clickobj.inner_url!="undefined"){
                    $("#adclickType").val(4);
                }
                if(typeof clickobj.ad_list!="undefined"){
                    $("#adclickType").val(5);
                }
                if(typeof clickobj.user_action!="undefined"){
                    $("#adclickType").val(6);
                    $("#adclickType_6").show();
                    $("#aduser_action_value").val(clickobj.user_action.user_action_value);
                }
                if(typeof clickobj.no_action!="undefined"){
                    $("#adclickType").val(100);
                    $("#adclickType_0").show();
                }
            }else{
                $("#adclickType").val(1);
            }
        };
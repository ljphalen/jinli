<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Doo::loadController("AppDooController");

class AdController extends AppDooController{
    public function del(){
        $adid=$this->get["adid"];
        $ad=Doo::loadModel("datamodel/adConfig",TRUE);
        $ad->del($adid);
    }
    public function edit(){
        $adid=intval($this->get["adid"]);
        $adinfoid=intval($this->get["adinfoid"]);
        $type=$this->get["type"];
        $ad=Doo::loadModel("Ad",TRUE);
        $result=$ad->getView($type,$adinfoid);
        $this->data["result"]=$result;
        $this->data["ad_id"]=$adid;
        //print_r($this->data);
        $this->myRenderWithoutTemplate("ad/detail",$this->data);
    }
    public function add(){
        $this->myRenderWithoutTemplate("ad/add",$this->data);
    }
    public function upd(){//更新广告
        $ad_id=  empty($this->post["ad_id"])?"":$this->post["ad_id"];
        $ad_info_id=  empty($this->post["ad_info_id"])?"":$this->post["ad_info_id"];
        $adtitle=$this->post["ad_title"];
        $addesc=$this->post["ad_desc"];
        $adstate=$this->post["ad_state"];
        $ad_type=$this->post["ad_type"];
        $ad_nembdsubtype=$this->post["ad_nembdsubtype"];
        $ad_embdsubtype=$this->post["ad_embdsubtype"];
        $ad_push=$this->post["ad_push"];
        $adclictypeobj=  stripcslashes($this->post["clictypeobj"]);
        $ad_target=$this->post["ad_target"];
        switch ($ad_type){
            case 0:
                $ad_subtype=$ad_nembdsubtype;
                break;
            case 1:
                $ad_subtype=$ad_embdsubtype;
                break;
            case 2:
                $ad_subtype=$ad_push;
                break;
            default :
                $ad_subtype=$ad_nembdsubtype;
                break;
        }
        $ad_pos=$this->post["ad_pos"];
        $ad_pic_url=$this->post["ad_pic_url"];
        $ad_product_id=empty($this->post["ad_product_id"])?"":$this->post["ad_product_id"];
        $show_detail=$this->post["p_desc_show"];
        $ad_rate="";
        //抢占式
        $show_time=  empty($this->post["show_time"])?"":$this->post["show_time"];
        $screen_type=empty($this->post["screen_type"])?"":$this->post["screen_type"];
        $screen_ratio=empty($this->post["screen_ratio"])?"":$this->post["screen_ratio"];
        //嵌入式
        $ad_name=empty($this->post["ad_name"])?null:$this->post["ad_name"];
        $ad_subdesc=  empty($this->post["ad_subdesc"])?"":$this->post["ad_subdesc"];
        
        $ad=Doo::loadModel("Ad",TRUE);
        $result=$ad->addAdinfo($ad_product_id,$adtitle,$addesc,$adstate,$ad_type,$ad_pos,$show_detail,$adclictypeobj,$ad_target,$ad_info_id);
        if(!$result){
            $this->showMsg("更新失败");
        }
        if(empty($ad_info_id)){
            $ad_info_id=$result;
            if($ad_type==0){
                $rate=$ad->getAdNotEmbeddedinfo($ad_info_id);
            }
            if($ad_type==1 || $ad_type==2){
                $rate=$ad->getAdEmbeddedinfo($ad_info_id);
            }
            if(!empty($rate)){
                $ad_rate=$rate["rate"];
            }
        }
        
        if($ad_type==="0"){//抢占式的调用抢占式
            $ad_id=$ad->updAdNotEmbeddedinfo($ad_subtype,$ad_pic_url,$screen_ratio,$show_time,$screen_type,$rate=0,$ad_info_id,$ad_id);
        }
        if($ad_type==="1" || $ad_type==="2"){//嵌入式和PUSH走此通道
            $ad_id=$ad->updAdEmbeddedinfo($ad_subtype,$ad_pic_url,$ad_name,$ad_subdesc,$screen_ratio,$rate=0,$ad_info_id,$ad_id);
        }
        
        //{"type":1,"typename":"类型名","subtype":0,"subtypename":"子类型名","ad_id":3,"ad_state":1,"ad_name":"标题","ad_rate":""}
        $ad_type_cate=Doo::conf()->AD_TYPE_CATE;
        $data=array("type"=>$ad_type,"subtype"=>$ad_subtype,"subtypename"=>$ad_type_cate[$ad_type]["subtype"][$ad_subtype],"ad_id"=>$ad_id,"ad_info_id"=>$ad_info_id,"ad_state"=>$adstate,"ad_name"=>$adtitle,"ad_rate"=>$ad_rate,"show_detail"=>$show_detail);
        $this->showData($data);
    }
    public function updadconfig(){
        $config_name=$_POST["config_name"];
        $config_desc=$_POST["config_desc"];
        $appkey=$_POST["appkey"];
        $channel_id=$_POST["channel_id"];
        $product_comb=$_POST["product_comb"];
        $config_level=$_POST["config_lelvel"];
        $config_type=$_POST["config_type"];
        $config_deital=$_POST["config_deital"];
        $platform=$_POST["platform"];
        
        $configdetails=Doo::loadModel("AdConfigDetails",TRUE);
        $configdetails->type=$config_level;
        $configdetails->type_value=$config_deital;
        $configdetails->created=time();
        $configdetails->updated=time();
        $config_detail_id=$config_deital->insert();
        
        $config=Doo::loadModel("AdConfigs",TRUE);
        $config->up($name,$desc,$product_comb,$config_detail_id,$config_detail_type,$config_type,$platform);
        
        return true;
    }
}
?>

<?php
Doo::loadModel('datamodel/base/AdsInfoBase');

class AdsInfo extends AdsInfoBase{
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("ads");
    }
    public function getAdinfo($where){
        $whereSql=$this->conditions($where, $this->_fields);
        $sql="select * from ads_info where ".$whereSql;
        $adinfo=Doo::db()->query($sql)->fetch();
        return $adinfo;
    }
    public function upd($post){
        if(empty($post)|| !is_array($post)){
            return false;
        }
        
        $ppost=$this->_pasrsePostdata($post);

        Doo::db()->query("update ads_info set del=-1 where ad_product_id='".$post["product_id"]."'")->execute();
        $textmodel=Doo::loadModel("datamodel/AdText",TRUE);
        if(!empty($ppost["text"])){
            foreach ($ppost["text"] as $key=>$text){
                $textinfo=$textmodel->getOne(array("select"=>"*","where"=>"id='".$key."' and ischeck=1","asArray"=>TRUE));
                $adinfo=$this->getAdinfo(array("r_id"=>$key,"r_type"=>2));
                $this->ad_product_id=$textinfo["ad_product_id"];
                $this->ad_name=$textinfo["product_name"];
                if($textinfo["subtype"]==3){//3为自定义文案
                    $this->ad_desc=$textinfo["content"];
                }else{
                    $this->ad_desc=$textinfo["product_name"];
                }
                $this->ad_click_type_object=$text["ad_click_type_object"];
                $this->ad_target=$text["ad_target"];
                $this->rate=$text["text_rate"];
                $this->state=$text["text_state"];

                if($textinfo["subtype"]==3){//3为自定义文案
                    $this->pos="CUSTOM";
                    $this->type=3;
                }else{
                    $this->pos="BANNER";
                    $this->type=1;
                }

                $this->r_type=2;
                $this->r_id=$key;
                $this->updated=time();
                if(!empty($adinfo)){
                    $this->id=$adinfo["id"];
                    $this->del=1;
                    $this->update();
                }else{
                    $this->id="";
                    $this->created=time();
                    $this->insert();
                }
                array_push($textadid, $adinfo["id"]);
            }
        }
        
        if(!empty($ppost["pic"])){
            foreach ($ppost["pic"] as $k=>$pic){
                $picmodel=Doo::loadModel("datamodel/AdPic",TRUE);
                $picinfo=$picmodel->getOne(array("select"=>"*","where"=>"id in(".$k.") and ad_type!=10 and ischeck=1","asArray"=>TRUE));
                //***更新pic表屏幕显示比率
                $picmodel->screen_ratio=$pic["screen_ratio"];
                $picmodel->id=$k;
                $picmodel->update();
                $adinfo=$this->getAdinfo(array("r_id"=>$k,"r_type"=>1));
                $this->ad_product_id=$picinfo["ad_product_id"];
                $this->ad_name=$picinfo["pic_name"];
                $ad_desc=$picinfo["pic_name"];
                if($picinfo["ad_type"]==3){//如果是信息流广告这从text中随机随机选择一个bannertext作为描述
                    $sql="select ad_desc from mobgi_ads.ads_info where ad_product_id='".$post["product_id"]."' and state=1 and type=3 order by updated desc limit 1 ";
                    $ad_desc=Doo::db()->query($sql)->fetch();
                    $ad_desc=  empty($ad_desc)?$picinfo["pic_name"]:$ad_desc["ad_desc"];
                }

                $this->ad_desc=$ad_desc;
                $this->ad_click_type_object=$pic["ad_click_type_object"];
                $this->ad_target=$pic["ad_target"];
                if($picinfo["ad_type"]==0){//0为插屏
                        $this->pos="HALF";
                }elseif($picinfo["ad_type"]==1){
                    $this->pos="BANNER";
                }elseif($picinfo["ad_type"]==3){
                    $this->pos="CUSTOM";
                }

                //$this->screen_ratio=$pic["screen_ratio"];
                $this->show_time=$pic["pic_show_time"];
                $this->close_wait=$pic["close_wait"];
                $this->rate=$pic["pic_rate"];
                $this->state=$pic["pic_state"];
                $this->type=$picinfo["ad_type"];
                $this->r_id=$k;
                $this->r_type=1;
                $this->updated=time();
                if(!empty($adinfo)){
                    $this->id=$adinfo["id"];
                    $this->del=1;
                    $this->update();
                }else{
                    $this->id='';
                    $this->created=time();
                    $this->insert();
                }
            }
        }
        
        if(!empty($ppost["push"])){
            foreach ($ppost["push"] as $key=>$push){
                $textmodel->ad_product_id=$post["id"];
                $textmodel->product_name=$post["pname"];
                $textmodel->subtype=2;
                $textmodel->ad_name=$push["ad_name"];
                $textmodel->content=$push["content"];
                $textmodel->checker=$post["checker"];
                $textmodel->ischeck=1;
                $textmodel->checker=$post["checker"];
                $textmodel->owner=$post["owner"];
                $textmodel->check_msg=$post["check_msg"];

                if(empty($key)){//新增
                    $textmodel->id="";
                    $textmodel->createtime=time();
                    $pushid=$textmodel->insert();
                }else{
                    $pushid=$key;
                    $textmodel->id=$pushid;
                    $textmodel->update();
                }
                $this->ad_product_id=$post["id"];
                $this->ad_name=$post["pname"];
                $this->ad_desc=$post["pname"];
                $this->ad_click_type_object=$push["ad_click_type_object"];
                $this->ad_target=$push["ad_target"];
                $this->rate=$push["push_rate"];
                $this->state=$push["push_state"];

                $this->pos="PUSH";
                $this->type=2;//push类型
                $this->r_type=2;
                $this->r_id=$pushid;
                $this->updated=time();

                if(!empty($key)){
                    $this->id=$push["pushaid"];
                    $this->del=1;
                    $this->update();
                }else{
                    $this->id="";
                    $this->created=time();
                    $this->insert();
                }
            }
        }
        
        if(!empty($ppost["html"])){
            foreach ($ppost["html"] as $k=>$html){
                $htmlmodel=Doo::loadModel("datamodel/AdHtml",TRUE);
                $htmlinfo=$htmlmodel->getOne(array("select"=>"*","where"=>"id in(".$k.") and ischeck=1","asArray"=>TRUE));
                //***更新pic表屏幕显示比率
                $htmlmodel->screen_ratio=$html["screen_ratio"];
                $htmlmodel->id=$k;
                $htmlmodel->update();
                $adinfo=$this->getAdinfo(array("r_id"=>$k,"r_type"=>4));
                $this->ad_product_id=$htmlinfo["ad_product_id"];
                $this->ad_name=$htmlinfo["html_name"];
                $ad_desc=$htmlinfo["html_name"];

                $this->ad_desc=$ad_desc;
                $this->ad_click_type_object=$html["ad_click_type_object"];
                $this->ad_target=$html["ad_target"];
                if($htmlinfo["ad_type"]==0){//0为插屏
                        $this->pos="HALF";
                }elseif($htmlinfo["ad_type"]==1){
                    $this->pos="BANNER";
                }
                //$this->screen_ratio=$html["screen_ratio"];
                $this->show_time=$html["html_show_time"];
                $this->rate=$html["html_rate"];
                $this->state=$html["html_state"];
                $this->type=$htmlinfo["ad_type"];
                $this->close_wait=$html["html_close_wait"];
                $this->r_id=$k;
                $this->r_type=4;
                $this->updated=time();
                if(!empty($adinfo)){
                    $this->id=$adinfo["id"];
                    $this->del=1;
                    $this->update();
                }else{
                    $this->id='';
                    $this->created=time();
                    $this->insert();
                }
            }
        }
        
        if(!empty($ppost["video"])){            
            foreach ($ppost["video"] as $k=>$video){
                $videomodel = Doo::loadModel("datamodel/AdIncentiveVideo",TRUE);
                $videoinfo=$videomodel->getOne(array("select"=>"*","where"=>"id in(".$k.") and ischeck=1","asArray"=>TRUE));
                $adinfo=$this->getAdinfo(array("r_id"=>$k, "r_type"=>6));
                $this->ad_product_id=$videoinfo["ad_product_id"];
                $this->ad_name=$video["incentive_video_name"];
                $this->ad_desc=$video["incentive_video_name"];
                $this->rate=$video["incentive_video_rate"];
                $this->state=$video["incentive_video_state"];
                $this->type=4;
                $this->r_id=$k;
                $this->r_type=6;
                $this->updated=time();
                if(!empty($adinfo)){
                    $this->id=$adinfo["id"];
                    $this->del=1;
                    $this->update();
                }else{
                    $this->id='';
                    $this->created=time();
                    $this->insert();
                }
            }
        }
    }
    
    public function getVedioResourceByproductid($productid){
        $videoinfo=$this->find(array("select"=>"*","where"=>"ad_product_id='".$productid."' and r_type=6 and del=1","desc"=>"state","asArray"=>TRUE));
        $videomodel=Doo::loadModel("datamodel/AdIncentiveVideo",TRUE);
        if(!empty($videoinfo)){
            foreach($videoinfo as $k=>$video){
                $result=$videomodel->getOne(array("select"=>"*","where"=>"id in(".  $video["r_id"].") and ischeck=1","asArray"=>TRUE));
                $videoinfo[$k]["video"]=$result;
            }
        }
       return$videoinfo;
    }
    
    
    public function getResourceByproductid($productid){
        $picinfo=$this->find(array("select"=>"*","where"=>"ad_product_id='".$productid."' and r_type=1 and del=1","desc"=>"state","asArray"=>TRUE));
        $htmlinfo=$this->find(array("select"=>"*","where"=>"ad_product_id='".$productid."' and r_type=4 and del=1","desc"=>"state","asArray"=>TRUE));
        $textinfo=$this->find(array("select"=>"*","where"=>"ad_product_id='".$productid."' and r_type in(2,3)  and del=1","asc"=>"state","asArray"=>TRUE));
        $picmodel=Doo::loadModel("datamodel/AdPic",TRUE);
        $htmlmodel=Doo::loadModel("datamodel/AdHtml",TRUE);
        $textmodel=Doo::loadModel("datamodel/AdText",TRUE);
        
        if(!empty($picinfo)){
            foreach($picinfo as $k=>$pic){
                $result=$picmodel->getOne(array("select"=>"*","where"=>"id in(".  $pic["r_id"].") and ischeck=1","asArray"=>TRUE));
                $picinfo[$k]["pic"]=$result;
            }
        }
        if(!empty($htmlinfo)){
            foreach($htmlinfo as $k=>$html){
                $result=$htmlmodel->getOne(array("select"=>"*","where"=>"id in(".  $html["r_id"].") and ischeck=1","asArray"=>TRUE));
                $htmlinfo[$k]["html"]=$result;
            }
        }
 
        if(!empty($textinfo)){
            foreach($textinfo as $k=>$text){
                $result=$textmodel->getOne(array("select"=>"*","where"=>"id in(".  $text["r_id"].") and ischeck=1","asArray"=>TRUE));
                $textinfo[$k]["text"]=$result;
            }
        }
        
        return array_merge($picinfo,$textinfo,$htmlinfo);
    }
    
 public function resource2adsinfo($post){
      $product_id=$post["id"];
      $adsinfo=$this->find(array("select"=>"*","where"=>"ad_product_id='".$product_id."'","asArray"=>TRUE));

      
      if(empty($adsinfo)){return false;}
      $adinfo=Doo::loadModel("datamodel/AdInfo",TRUE);
      
      $adid=$adinfo->find(array("select"=>"id","where"=>"ad_product_id='".$product_id."'  and state=1","asArray"=>TRUE));
      $adid=  array2one($adid, "id");
      $diffadid=array_diff($adid, $post["adid"]);
      if(!empty($diffadid)){
          $sql="update mobgi_api.ad_info set state=0 where id in(".  implode(",",$diffadid).")";
          Doo::db()->query($sql)->execute();
      }
      foreach($adsinfo as $ad){
        $sql="INSERT INTO mobgi_api.`ad_info` (id,ad_product_id,ad_name,ad_desc,ad_click_type_object,ad_target,state,pos,type,created)"
            . " VALUES(%d,%d,'%s','%s','%s','%s','%s','%s','%s',UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE id=%d,ad_product_id=%d,ad_name='%s',ad_desc='%s',ad_click_type_object='%s',ad_target='%s',state='%s',pos='%s',type='%s',updated=UNIX_TIMESTAMP(NOW())";
        $sql=  sprintf($sql,$ad["id"],$ad["ad_product_id"],$ad["ad_name"],$ad["ad_desc"],$ad["ad_click_type_object"],$ad["ad_target"],$ad["state"],$ad["pos"],$ad["type"]
                    ,$ad["id"],$ad["ad_product_id"],$ad["ad_name"],$ad["ad_desc"],$ad["ad_click_type_object"],$ad["ad_target"],$ad["state"],$ad["pos"],$ad["type"]);
        Doo::db()->query($sql)->execute();
        $adid=$ad["id"];
        $admodel=Doo::loadModel("Ad",TRUE);
        if($ad["del"]==-1){
            $sql="delete from mobgi_api.ad_info where id='".$ad["id"]."'";
            Doo::db()->query($sql)->execute();
        }
        //$admodel->addAdinfo($adsinfo["ad_product_id"],$adsinfo["ad_name"],$adsinfo["ad_desc"],$adsinfo["state"],$adsinfo["type"],$adsinfo["pos"],"",$adsinfo["ad_click_type_object"],$adsinfo["ad_target"],$adsinfo["id"]);
        if($ad["r_type"]==1){//图片素材 大类0,小类0
            //先删除  
            $sql="delete from mobgi_api.ad_not_embedded_info where ad_info_id='".$ad["id"]."'";
            Doo::db()->query($sql)->execute();
            if($ad["del"]==-1){
                continue;
            }
            $adpic=Doo::loadModel("datamodel/AdPic",TRUE);
            //取插页广告,BANNER
            $adpicinfo=$adpic->getOne(array("select"=>"*","where"=>"id=".$ad["r_id"]." and ad_type in(0,1,3)","asArray"=>TRUE));
            if(empty($adpicinfo)){continue;}
            if($adpicinfo["ad_type"]==0){
                $admodel->updAdNotEmbeddedinfo(0,$adpicinfo["pic_url"],$adpicinfo["screen_ratio"],$ad["show_time"],$adpicinfo["ad_subtype"],$ad["rate"],$adid,$id=null,$adpicinfo["resolution"],$ad["close_wait"]);
            }elseif($adpicinfo["ad_type"]==3){
                $sql="delete from mobgi_api.ad_customized_info where ad_info_id='".$ad["id"]."'";
                Doo::db()->query($sql)->execute();
                $admodel->updAdCustomizedinfo(0,$adpicinfo["pic_url"],$ad["ad_desc"],$adpicinfo["screen_ratio"],$ad["rate"],$adid,$id=null,$adpicinfo["resolution"]);
            }else{
                $sql="delete from mobgi_api.ad_embedded_info where ad_info_id='".$ad["id"]."'";
                Doo::db()->query($sql)->execute();
                $admodel->updAdEmbeddedinfo(0,$adpicinfo["pic_url"],$ad["ad_name"],$ad["ad_desc"],$adpicinfo["screen_ratio"],$ad["rate"],$adid,$id=null,$adpicinfo["resolution"]);
            }
        }else if($ad["r_type"]==2){//文案素材 大类1,小类0
            //先删除  
            $sql="delete from mobgi_api.ad_embedded_info where ad_info_id='".$ad["id"]."'";
            Doo::db()->query($sql)->execute();
            if($ad["del"]==-1){
                continue;
            }
            $text=Doo::loadModel("datamodel/AdText",TRUE);
            $textinfo=$text->getOne(array("select"=>"*","where"=>"id='".$ad["r_id"]."' and subtype=".$ad["type"],"asArray"=>TRUE));
            if(empty($textinfo)){continue;}
            if($ad["type"]==2){//如果push消息 大类2,小类0
                $adsubtype=0;
            }else{
                $adsubtype=1;
            }
            $productsobj=Doo::loadModel("datamodel/AdsProductInfo",TRUE);
            $productsinfo=$productsobj->getOne(array("select"=>"*","where"=>"id=".$product_id,"asArray"=>TRUE));
            $admodel->updAdEmbeddedinfo($adsubtype,$productsinfo["product_icon"],$textinfo["ad_name"],$textinfo["content"],'',$ad["rate"],$adid,$id=null);
        }else if($ad["r_type"]==4){//网页素材 大类1,小类0
            //先删除  
            $sql="delete from mobgi_api.ad_not_embedded_info where ad_info_id='".$ad["id"]."'";
            Doo::db()->query($sql)->execute();
            if($ad["del"]==-1){
                continue;
            }
            $adhtml=Doo::loadModel("datamodel/AdHtml",TRUE);
            //取插页广告,BANNER
            $adhtmlinfo=$adhtml->getOne(array("select"=>"*","where"=>"id=".$ad["r_id"]." and ad_type in(0,1)","asArray"=>TRUE));
            if(empty($adhtmlinfo)){continue;}
            if($adhtmlinfo["ad_type"]==0){
                $admodel->updAdNotEmbeddedinfo(3,$adhtmlinfo["html_url"],$adhtmlinfo["screen_ratio"],$ad["show_time"],2,$ad["rate"],$adid,$id=null,null,$ad["close_wait"]);
            }elseif($adhtmlinfo["ad_type"]==1){
                $sql="delete from mobgi_api.ad_embedded_info where ad_info_id='".$ad["id"]."'";
                Doo::db()->query($sql)->execute();
                $admodel->updAdEmbeddedinfo(4,$adhtmlinfo["html_url"],$ad["ad_name"],$ad["ad_desc"],$adhtmlinfo["screen_ratio"],$ad["rate"],$adid,$id=null,null);
            }
        }else if($ad["r_type"]==6){//视频素材 
            //先删除  
            $sql="delete from mobgi_api.ad_incentive_video_info where ad_info_id='".$ad["id"]."'";
            Doo::db()->query($sql)->execute();
            if($ad["del"]==-1){
                continue;
            }
            $advideo=Doo::loadModel("datamodel/AdIncentiveVideo",TRUE);
            $advideoinfo=$advideo->getOne(array("select"=>"*","where"=>"id=".$ad["r_id"],"asArray"=>TRUE));     
         
            $advideoInfoMode=Doo::loadModel("datamodel/AdIncentiveVideoInfo",TRUE);
             $advideoInfoMode->updAdIncentiveVedioInfo(0,$advideoinfo['h5_url'],$advideoinfo['video_url'],$ad['rate'],$adid,$id=null);
        }
      }
      return true;
    }
    //修改ads_info表
    public function updteads($post){
        $this->ad_product_id=$post["ad_product_id"];
        $this->ad_name=$post["ad_name"];
        $this->ad_desc=$post["ad_desc"];
        $this->ad_click_type_object=$post["ad_click_type_object"];
        $this->ad_target=$post["ad_target"];

        $this->screen_ratio=$post["screen_ratio"];
        $this->show_time=$post["show_time"];
        $this->rate=$post["rate"];
        $this->state=$post["state"];
        $this->pos=$post["pos"];
        $this->type=$post["type"];
        $this->r_id=$post["r_id"];
        $this->r_type=$post["r_type"];
        $this->updated=time();
        if(!empty($post["id"])){
            $this->id=$post["id"];
            $this->update();
        }else{
            $this->created=time();
            $this->insert();
        }
        return true;
    }
    /*
        根据素材类型删除解除素材和产品的关联
     *
     */
    
    public function delbytype($product_id,$adid,$rtype){
        $sql="select id from ads_info where r_type=$rtype and ad_product_id=$product_id";
        $result=Doo::db()->query($sql)->fetchAll();
        $result=array2one($result,"id");
        $id=array_diff($result,$adid);
        return $this->del($id);
    }
    public function del($id){
        try{
            if(is_array($id)){
                foreach($id as $v){
                    $this->id=$v;
                    $this->del=-1;
                    $this->update();
                }
            }else{
                $this->id=$id;
                $this->del=-1;
                $this->update();
            }
        }  catch (Exception $e){
            return false;
        }
        return true;
    }
    //处理post过来的数据
    private function _pasrsePostdata($post){
        
        $pic = $this->_parsePicPostData($post);
        $text = $this->_parseTextPostData($post);
        $push = $this->_parsePustPostData($post);
        $html = $this->_parseHtmlPostData($post);
        $video = $this->_parseVideoPostData($post);

        return array("pic"=>$pic,"text"=>$text,"push"=>$push,"html"=>$html,"video"=>$video);
    }
  
    private function _parseVideoPostData($post){
        if(!empty($post["incentive_video_id"]) && is_array($post["incentive_video_id"])){
            $video=array();
            foreach ($post["incentive_video_id"] as $k=>$v){
                $video[$v]["incentive_video_name"]=$post["incentive_video_name"][$k];
                $video[$v]["incentive_video_state"]=$post["incentive_video_state"][$k];
                $video[$v]["incentive_video_rate"]=$post["incentive_video_rate"][$k];
            }
        }
        return $video;
    }

    private function _parseHtmlPostData($post) {
        if(!empty($post["htmlid"]) && is_array($post["htmlid"])){
            $html=array();
            foreach ($post["htmlid"] as $k=>$v){
               // $pic[$v]["ad_type"]=$post[$k]["ad_type"];
                $html[$v]["html_state"]=$post["html_state"][$k];
                $html[$v]["ad_type"]=$post["ad_type"][$k];
                $html[$v]["screen_ratio"]=$post["screen_ratio"][$k];
                $html[$v]["html_show_time"]=$post["html_show_time"][$k];
                $html[$v]["html_rate"]=$post["html_rate"][$k];
                $html[$v]["ad_click_type_object"]=stripslashes($post["html_ad_click_type_object"][$k]);
                $html[$v]["ad_target"]=$post["html_ad_target"][$k];
                $html[$v]["close_wait"]=$post["html_close_wait"][$k];
            }
        }
        return $html;
    }

    
    private function _parsePustPostData($post) {
        if(!empty($post["pushid"]) && is_array($post["pushid"])){
            $push=array();
            foreach ($post["pushid"] as $k=>$v){
                $push[$v]["push_state"]=$post["push_state"][$k];
                $push[$v]["push_rate"]=$post["push_rate"][$k];
                $push[$v]["ad_click_type_object"]=stripslashes($post["push_ad_click_type_object"][$k]);
                $push[$v]["ad_target"]=$post["push_ad_target"][$k];
                $push[$v]["content"]=$post["content"][$k];
                $push[$v]["ad_name"]=$post["push_title"][$k];
                $push[$v]["pushaid"]=$post["pushaid"][$k];
            }
         }
         return $push;
    }
    
    private function _parseTextPostData($post)  {
        if(!empty($post["textid"]) && is_array($post["textid"])){
            $text=array();
            foreach ($post["textid"] as $k=>$v){
                $text[$v]["text_state"]=$post["text_state"][$k];
                $text[$v]["text_rate"]=$post["text_rate"][$k];
                $text[$v]["ad_click_type_object"]=stripslashes($post["text_ad_click_type_object"][$k]);
                $text[$v]["ad_target"]=$post["text_ad_target"][$k];
            }
        }
        return $text;
     }

private function _parsePicPostData($post){
    if(!empty($post["picid"]) && is_array($post["picid"])){
        $pic=array();
        foreach ($post["picid"] as $k=>$v){
           // $pic[$v]["ad_type"]=$post[$k]["ad_type"];
            $pic[$v]["pic_state"]=$post["pic_state"][$k];
            $pic[$v]["ad_type"]=$post["ad_type"][$k];
            $pic[$v]["screen_ratio"]=$post["screen_ratio"][$k];
            $pic[$v]["pic_show_time"]=$post["pic_show_time"][$k];
            $pic[$v]["pic_rate"]=$post["pic_rate"][$k];
            $pic[$v]["ad_click_type_object"]=stripslashes($post["pic_ad_click_type_object"][$k]);
            $pic[$v]["ad_target"]=$post["pic_ad_target"][$k];
            $pic[$v]["close_wait"]=$post["pic_close_wait"][$k];
        }
    }
    return $pic;
}

    
}
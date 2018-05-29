<?php
Doo::loadModel('datamodel/base/AdIncentiveVideoInfoBase');

class AdIncentiveVideoInfo extends AdIncentiveVideoInfoBase{
    
    public function __construct($properties = null) {
        parent::__construct($properties);
        
    }
    
    public function updAdIncentiveVedioInfo($type,$h5_url,$video_url,$rate=0,$ad_info_id,$id=null){
        
        $this->type=$type;
        $this->h5_url=$h5_url;
        $this->video_url=$video_url;
        $this->rate=$rate;
        $this->ad_info_id=$ad_info_id;
        $this->created=time();
        $this->updated=time();
        if(empty($id)){
            return $this->insert();
        }else{
            $this->id=$id;
            $this->update();
            return $id;
        }
    }

}
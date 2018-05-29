<?php
Doo::loadModel('datamodel/base/AdInfoBase');

class AdInfo extends AdInfoBase{
    
    /**
     * 查找PUSH配置某产品是否存在方案素材
     * @param type $productid
     * @return type
     */
    public function getPushAdsInfoByProductid($productid){
        $sql = ' select * from ad_info left join ad_embedded_info on ad_info.id = ad_embedded_info.ad_info_id where ad_info.type=2  and ad_embedded_info.type=0 and ad_info.ad_product_id='.$productid;
        $result=Doo::db()->query($sql)->fetchAll();
        return $result;
    }
    
    /**
     * 判断是否存在文本广告
     * @param type $productid
     * @return type
     */
    public function getAdTextByProductid($productid){
        $sql = ' select * from ad_info left join ad_embedded_info on ad_info.id = ad_embedded_info.ad_info_id where ad_embedded_info.type=1 and ad_info.ad_product_id ='.$productid;
        $result=Doo::db()->query($sql)->fetchAll();
        return $result;
    }
    
}
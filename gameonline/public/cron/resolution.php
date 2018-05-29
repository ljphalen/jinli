<?php
include 'common.php';
/**
 *update resolution
 *
 */

//更改分辨率
Resource_Service_Attribute::updateResourceAttribute(array('id'=>110,'title'=>'240*320'),6);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>115,'title'=>'320*480'),7);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>120,'title'=>'480*728'),33);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>125,'title'=>'480*800'),9);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>130,'title'=>'480*854'),10);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>135,'title'=>'540*960'),11);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>140,'title'=>'720*1184'),34);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>145,'title'=>'720*1280'),12);
Resource_Service_Attribute::updateResourceAttribute(array('id'=>150,'title'=>'1080*1920'),37);


//更改分辨率索引表

Resource_Service_Games::updateBy(array('max_resolution'=>110), array('max_resolution'=>6));
Resource_Service_Games::updateBy(array('min_resolution'=>110), array('min_resolution'=>6));

Resource_Service_Games::updateBy(array('max_resolution'=>115), array('max_resolution'=>7));
Resource_Service_Games::updateBy(array('min_resolution'=>115), array('min_resolution'=>7));

Resource_Service_Games::updateBy(array('max_resolution'=>120), array('max_resolution'=>33));
Resource_Service_Games::updateBy(array('min_resolution'=>120), array('min_resolution'=>33));

Resource_Service_Games::updateBy(array('max_resolution'=>125), array('max_resolution'=>9));
Resource_Service_Games::updateBy(array('min_resolution'=>125), array('min_resolution'=>9));

Resource_Service_Games::updateBy(array('max_resolution'=>130), array('max_resolution'=>10));
Resource_Service_Games::updateBy(array('min_resolution'=>130), array('min_resolution'=>10));

Resource_Service_Games::updateBy(array('max_resolution'=>135), array('max_resolution'=>11));
Resource_Service_Games::updateBy(array('min_resolution'=>135), array('min_resolution'=>11));

Resource_Service_Games::updateBy(array('max_resolution'=>140), array('max_resolution'=>34));
Resource_Service_Games::updateBy(array('min_resolution'=>140), array('min_resolution'=>34));

Resource_Service_Games::updateBy(array('max_resolution'=>145), array('max_resolution'=>12));
Resource_Service_Games::updateBy(array('min_resolution'=>145), array('min_resolution'=>12));

Resource_Service_Games::updateBy(array('max_resolution'=>150), array('max_resolution'=>37));
Resource_Service_Games::updateBy(array('min_resolution'=>150), array('min_resolution'=>37));

//更改机型表
Resource_Service_Type::updateBy(array('resolution'=>110),array('resolution'=>6));
Resource_Service_Type::updateBy(array('resolution'=>115),array('resolution'=>7));
Resource_Service_Type::updateBy(array('resolution'=>120),array('resolution'=>33));
Resource_Service_Type::updateBy(array('resolution'=>125),array('resolution'=>9));
Resource_Service_Type::updateBy(array('resolution'=>130),array('resolution'=>10));
Resource_Service_Type::updateBy(array('resolution'=>135),array('resolution'=>11));
Resource_Service_Type::updateBy(array('resolution'=>140),array('resolution'=>34));
Resource_Service_Type::updateBy(array('resolution'=>145),array('resolution'=>12));
Resource_Service_Type::updateBy(array('resolution'=>150),array('resolution'=>37));

echo CRON_SUCCESS;
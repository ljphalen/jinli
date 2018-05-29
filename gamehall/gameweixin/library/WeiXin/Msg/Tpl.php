<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * WeiXin_Msg_Tpl
 *
 * 消息
 */
class WeiXin_Msg_Tpl
{
    //文本消息模板
    const TextTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
    
    //登陆消息内容模板
    const LoginContentTpl = "您尚未绑定您Amigo账号，为方便您更好的享受我们的专属服务，请先进行绑定！
 
<a href='%s'>点击这里</a>  即可完成绑定！";
    
    //A币消息内容模板
    const ACionContentTpl= "您的Amigo账号：%s
当前A币余额：%s
当前可用A券余额：%s%s";

    
    const AcionHelp = "

回复下列序号，获取更多服务：%s"; 
    
    static $helpArray = array(
    	            '1'=>"A币\\A券是什么",
                    '2'=>"如何获得A币",
                    '3'=>"如何获得A券",
                    '4'=>"关于我们"
    );
    
    //A券过期消息内容模板
    const ATickWillPastContentTpl = "%s，%s元即将到期；";
    
    //我的礼包消息内容模板
    const MyGiftContentTpl = "最新礼包：
%s%s";
    const MyAllGiftLinkTpl = "
<a href='%s'>查看我的全部礼包</a>";
    
    //我的礼包内容item膜拜
    const MyGiftItemContentTpl = "%s.%s
兑换码：%s
点击  <a href='%s'>查看详情</a>
";
    
    //图文消息模板
    const ArtiTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%d</ArticleCount>
<Articles>
%s
</Articles>
</xml>";
    
    //图文消息列表
    const ArtiItemTpl = "<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>";

    //消息转发多客服
    const CustomerTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";

    /**
     *   
     */
    public function getCustomerXML($msg) {
        return sprintf(self::CustomerTpl, $msg['FromUserName'], $msg['ToUserName'], time());
    }

    /**文本消息
     * @param $msg
     * @param $content
     * @return string
     */
    public function getTextXML($msg, $content)
    {
        return sprintf(self::TextTpl, $msg['FromUserName'], $msg['ToUserName'], time(), $content);
    }

    public function getACionTextXML($msg, $bindInfo, $helpIndexs) {
        $aTickInfo = $bindInfo['ATick'];
        if ($bindInfo['willExpireTick'] > 0) {
        	$aTickInfo = sprintf(self::ATickWillPastContentTpl, $bindInfo['ATick'], $bindInfo['willExpireTick']);
        }
        $helpStr = $this->getHelps($helpIndexs);
        $content = sprintf(self::ACionContentTpl, $bindInfo['uname'], $bindInfo['ACoin'], $aTickInfo, $helpStr);
        
        return sprintf(self::TextTpl, $msg['FromUserName'], $msg['ToUserName'], time(), $content);
    }
    
    private function getHelps($helpIndexs) {
        if (!$helpIndexs) {
        	return '';
        }
        $helpStr = '';
        foreach ($helpIndexs as $index) {
            if (self::$helpArray[$index]) {
                $helpStr .= ("\n".$index."  ".(self::$helpArray[$index]));
            }
        }
        if ($helpStr) {
        	return sprintf(self::AcionHelp, $helpStr);
        }
        return '';
    }
    
    public function getMyGiftTextXml($msg, $myGift, $myGiftListUrl) {
        $giftListStr = '';
        $allGiftLinkStr = '';
        if ($myGift) {
            $count = 1;
            foreach ($myGift as $gift) {
                $giftListStr .= sprintf(self::MyGiftItemContentTpl, $count, $gift['title'], $gift['code'], $gift['url']);
                $count ++;
            }
            $allGiftLinkStr = sprintf(self::MyAllGiftLinkTpl, $myGiftListUrl);
        } else {
            $giftListStr = "\n暂无礼包!\n";
        }
        $content = sprintf(self::MyGiftContentTpl, $giftListStr, $allGiftLinkStr);
        return sprintf(self::TextTpl, $msg['FromUserName'], $msg['ToUserName'], time(), $content);
    }
    
    public function getLoginTextXml($msg) {
        $openId = $msg['FromUserName'];
        $content = sprintf(self::LoginContentTpl, Admin_Service_Weixinuser::getLoginUrl($openId));
        return sprintf(self::TextTpl, $msg['FromUserName'], $msg['ToUserName'], time(), $content);
    }
    
    public function getTextJSON($msg, $content) {
        $data = array(
            'touser'=>$msg['FromUserName'],
            'msgtype'=>'text',
            'text'=>array(
                'content'=>$content
            )
        );
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    


    /**
     * 图文消息
     * @param $msg
     * @param $items
     * @return string
     */
    public function getArtXML($msg, $items)
    {
        $item_xml = "";
        foreach ($items as $key => $value) {
            $item_xml .= sprintf(self::ArtiItemTpl, $value["title"], $value["description"], $value["picurl"], $value["url"]);
        }
        return sprintf(self::ArtiTpl, $msg['FromUserName'], $msg['ToUserName'], time(), count($items), $item_xml);

    }

    public function getArtJSON($msg, $items) {

        $articles = array();
        foreach($items as $key=>$value) {
            $articles[] = array(
                'title'=>$value['title'],
                'description'=>$value['description'],
                'url'=>$value['url'],
                'picurl'=>$value['picurl']
            );
        }

        $data = array(
            'touser'=>$msg['ToUserName'],
            'msgtype'=>'news',
            'news'=>array(
                'articles'=>$articles
            )
        );
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

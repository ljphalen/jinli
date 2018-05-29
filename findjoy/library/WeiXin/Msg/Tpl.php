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

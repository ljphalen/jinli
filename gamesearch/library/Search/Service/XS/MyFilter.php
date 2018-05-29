<?php
class MyFilter implements XSDataFilter
{
    public function process($data, $cs =false)
    {
        $data['content'] = $this->filterHtml($data['content']);
        return $data;
    }

    public function processDoc($doc)
    {
        // $doc->addTerm('subject', '特殊词');
    }

    private function filterHtml($str)
    {
        $str=eregi_replace("<\/*[^<>]*>", '', $str);
        $str=str_replace(" ", '', $str);
        $str=str_replace("\n", '', $str);
        $str=str_replace("\t", '', $str);
        $str=str_replace("::", ':', $str);
        $str=str_replace(" ", '', $str);
        return $str;
    }
}
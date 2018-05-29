<?php
class XS_TokenizerNone implements XS_Tokenizer
{
    public function getTokens($value, XS_Document $doc = null)
    {
        return array();
    }
}

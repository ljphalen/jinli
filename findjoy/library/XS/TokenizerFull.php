<?php
class XS_TokenizerFull implements XS_Tokenizer
{
    public function getTokens($value, XS_Document $doc = null)
    {
        return array($value);
    }
}

<?php
interface XS_Tokenizer
{
    const DFL = 0;
    public function getTokens($value, XS_Document $doc = null);
}

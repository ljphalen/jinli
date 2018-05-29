<?php
class XS_TokenizerXstep implements XS_Tokenizer
{
    private $arg = 2;
    public function __construct($arg = null)
    {
        if ($arg !== null && $arg !== '') {
            $this->arg = intval($arg);
            if ($this->arg < 1 || $this->arg > 255) {
                throw new XS_Exception('Invalid argument for ' . __CLASS__ . ': ' . $arg);
            }
        }
    }
    public function getTokens($value, XS_Document $doc = null)
    {
        $terms = array();
        $i = $this->arg;
        while (true) {
            $terms[] = substr($value, 0, $i);
            if ($i >= strlen($value)) {
                break;
            }
            $i += $this->arg;
        }
        return $terms;
    }
}

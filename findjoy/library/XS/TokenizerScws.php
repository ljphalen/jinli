<?php
class XS_TokenizerScws implements XS_Tokenizer
{
    const MULTI_MASK = 15;
    private static $_charset;
    private $_setting = array();
    private static $_server;
    public function __construct($arg = null)
    {
        if (self::$_server === null) {
            $xs = XS_Main::getLastXS();
            if ($xs === null) {
                throw new XS_Exception('An XS instance should be created before using ' . __CLASS__);
            }
            self::$_server = $xs->getScwsServer();
            self::$_server->setTimeout(0);
            self::$_charset = $xs->getDefaultCharset();
        }
        if ($arg !== null && $arg !== '') {
            $this->setMulti($arg);
        }
    }
    public function getTokens($value, XS_Document $doc = null)
    {
        $tokens = array();
        $this->setIgnore(true);
        $_charset = self::$_charset;
        self::$_charset = 'UTF-8';
        $words = $this->getResult($value);
        foreach ($words as $word) {
            $tokens[] = $word['word'];
        }
        self::$_charset = $_charset;
        return $tokens;
    }
    public function setCharset($charset)
    {
        self::$_charset = strtoupper($charset);
        if (self::$_charset == 'UTF8') {
            self::$_charset = 'UTF-8';
        }
        return $this;
    }
    public function setIgnore($yes = true)
    {
        $this->_setting['ignore'] = new XS_Command(XS_Config::CMD_SEARCH_SCWS_SET, XS_Config::CMD_SCWS_SET_IGNORE, $yes === false
            ? 0 : 1);
        return $this;
    }
    public function setMulti($mode = 3)
    {
        $mode = intval($mode) & self::MULTI_MASK;
        $this->_setting['multi'] = new XS_Command(XS_Config::CMD_SEARCH_SCWS_SET, XS_Config::CMD_SCWS_SET_MULTI, $mode);
        return $this;
    }
    public function setDict($fpath, $mode = null)
    {
        if (!is_int($mode)) {
            $mode = stripos($fpath, '.txt') !== false ? SCWS_XDICT_TXT : SCWS_XDICT_XDB;
        }
        $this->_setting['set_dict'] = new XS_Command(XS_Config::CMD_SEARCH_SCWS_SET, XS_Config::CMD_SCWS_SET_DICT, $mode, $fpath);
        unset($this->_setting['add_dict']);
        return $this;
    }
    public function addDict($fpath, $mode = null)
    {
        if (!is_int($mode)) {
            $mode = stripos($fpath, '.txt') !== false ? SCWS_XDICT_TXT : SCWS_XDICT_XDB;
        }
        if (!isset($this->_setting['add_dict'])) {
            $this->_setting['add_dict'] = array();
        }
        $this->_setting['add_dict'][] = new XS_Command(XS_Config::CMD_SEARCH_SCWS_SET, XS_Config::CMD_SCWS_ADD_DICT, $mode, $fpath);
        return $this;
    }
    public function setDuality($yes = true)
    {
        $this->_setting['duality'] = new XS_Command(XS_Config::CMD_SEARCH_SCWS_SET, XS_Config::CMD_SCWS_SET_DUALITY, $yes === false
            ? 0 : 1);
        return $this;
    }
    public function getVersion()
    {
        $cmd = new XS_Command(XS_Config::CMD_SEARCH_SCWS_GET, XS_Config::CMD_SCWS_GET_VERSION);
        $res = self::$_server->execCommand($cmd, XS_Config::CMD_OK_INFO);
        return $res->buf;
    }
    public function getResult($text)
    {
        $words = array();
        $text = $this->applySetting($text);
        $cmd = new XS_Command(XS_Config::CMD_SEARCH_SCWS_GET, XS_Config::CMD_SCWS_GET_RESULT, 0, $text);
        $res = self::$_server->execCommand($cmd, XS_Config::CMD_OK_SCWS_RESULT);
        while ($res->buf !== '') {
            $tmp = unpack('Ioff/a4attr/a*word', $res->buf);
            $tmp['word'] = XS_Main::convert($tmp['word'], self::$_charset, 'UTF-8');
            $words[] = $tmp;
            $res = self::$_server->getRespond();
        }
        return $words;
    }
    public function getTops($text, $limit = 10, $xattr = '')
    {
        $words = array();
        $text = $this->applySetting($text);
        $cmd = new XS_Command(XS_Config::CMD_SEARCH_SCWS_GET, XS_Config::CMD_SCWS_GET_TOPS, $limit, $text, $xattr);
        $res = self::$_server->execCommand($cmd, XS_Config::CMD_OK_SCWS_TOPS);
        while ($res->buf !== '') {
            $tmp = unpack('Itimes/a4attr/a*word', $res->buf);
            $tmp['word'] = XS_Main::convert($tmp['word'], self::$_charset, 'UTF-8');
            $words[] = $tmp;
            $res = self::$_server->getRespond();
        }
        return $words;
    }
    public function hasWord($text, $xattr)
    {
        $text = $this->applySetting($text);
        $cmd = new XS_Command(XS_Config::CMD_SEARCH_SCWS_GET, XS_Config::CMD_SCWS_HAS_WORD, 0, $text, $xattr);
        $res = self::$_server->execCommand($cmd, XS_Config::CMD_OK_INFO);
        return $res->buf === 'OK';
    }
    private function applySetting($text)
    {
        self::$_server->reopen();
        foreach ($this->_setting as $key => $cmd) {
            if (is_array($cmd)) {
                foreach ($cmd as $_cmd) {
                    self::$_server->execCommand($_cmd);
                }
            } else {
                self::$_server->execCommand($cmd);
            }
        }
        return XS_Main::convert($text, 'UTF-8', self::$_charset);
    }
}

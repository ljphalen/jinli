<?php

class XS_Main extends XS_Component
{
    private $_index;
    private $_search;
    private $_scws;
    private $_scheme, $_bindScheme;
    private $_config;
    private static $_lastXS;
    public function __construct($file)
    {
        $this->loadIniFile($file);
        self::$_lastXS = $this;
    }
    public function __destruct()
    {
        $this->_index = null;
        $this->_search = null;
    }
    public static function getLastXS()
    {
        return self::$_lastXS;
    }
    public function getScheme()
    {
        return $this->_scheme;
    }
    public function setScheme(XS_FieldScheme $fs)
    {
        $fs->checkValid(true);
        $this->_scheme = $fs;
        if ($this->_search !== null) {
            $this->_search->markResetScheme();
        }
    }
    public function restoreScheme()
    {
        if ($this->_scheme !== $this->_bindScheme) {
            $this->_scheme = $this->_bindScheme;
            if ($this->_search !== null) {
                $this->_search->markResetScheme(true);
            }
        }
    }
    public function getConfig()
    {
        return $this->_config;
    }
    public function getName()
    {
        return $this->_config['project.name'];
    }
    public function setName($name)
    {
        $this->_config['project.name'] = $name;
    }
    public function getDefaultCharset()
    {
        return isset($this->_config['project.default_charset']) ?
            strtoupper($this->_config['project.default_charset']) : 'UTF-8';
    }
    public function setDefaultCharset($charset)
    {
        $this->_config['project.default_charset'] = strtoupper($charset);
    }
    public function getIndex()
    {
        if ($this->_index === null) {
            $adds = array();
            $conn = isset($this->_config['server.index']) ? $this->_config['server.index'] : 8383;
            if (($pos = strpos($conn, ';')) !== false) {
                $adds = explode(';', substr($conn, $pos + 1));
                $conn = substr($conn, 0, $pos);
            }
            $this->_index = new XS_Index($conn, $this);
            $this->_index->setTimeout(0);
            foreach ($adds as $conn) {
                $conn = trim($conn);
                if ($conn !== '') {
                    $this->_index->addServer($conn)->setTimeout(0);
                }
            }
        }
        return $this->_index;
    }
    public function getSearch()
    {
        if ($this->_search === null) {
            $conns = array();
            if (!isset($this->_config['server.search'])) {
                $conns[] = 8384;
            } else {
                foreach (explode(';', $this->_config['server.search']) as $conn) {
                    $conn = trim($conn);
                    if ($conn !== '') {
                        $conns[] = $conn;
                    }
                }
            }
            if (count($conns) > 1) {
                shuffle($conns);
            }
            for ($i = 0; $i < count($conns); $i++) {
                try {
                    $this->_search = new XS_Search($conns[$i], $this);
                    $this->_search->setCharset($this->getDefaultCharset());
                    return $this->_search;
                } catch (XS_Exception $e) {
                    if (($i + 1) === count($conns)) {
                        throw $e;
                    }
                }
            }
        }
        return $this->_search;
    }
    public function getScwsServer()
    {
        if ($this->_scws === null) {
            $conn = isset($this->_config['server.search']) ? $this->_config['server.search'] : 8384;
            $this->_scws = new XS_Server($conn, $this);
        }
        return $this->_scws;
    }
    public function getFieldId()
    {
        return $this->_scheme->getFieldId();
    }
    public function getFieldTitle()
    {
        return $this->_scheme->getFieldTitle();
    }
    public function getFieldBody()
    {
        return $this->_scheme->getFieldBody();
    }
    public function getField($name, $throw = true)
    {
        return $this->_scheme->getField($name, $throw);
    }
    public function getAllFields()
    {
        return $this->_scheme->getAllFields();
    }
    public static function convert($data, $to, $from)
    {
        if ($to == $from) {
            return $data;
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::convert($value, $to, $from);
            }
            return $data;
        }
        if (is_string($data) && preg_match('/[\x81-\xfe]/', $data)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($data, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to . '//TRANSLIT', $data);
            } else {
                throw new XS_Exception('Cann\'t find the mbstring or iconv extension to convert encoding');
            }
        }
        return $data;
    }
    private function parseIniData($data)
    {
        $ret = array();
        $cur = &$ret;
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            if ($line === '' || $line[0] == ';' || $line[0] == '#') {
                continue;
            }
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if ($line[0] === '[' && substr($line, -1, 1) === ']') {
                $sec = substr($line, 1, -1);
                $ret[$sec] = array();
                $cur = &$ret[$sec];
                continue;
            }
            if (($pos = strpos($line, '=')) === false) {
                continue;
            }
            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1), " '\t\"");
            $cur[$key] = $value;
        }
        return $ret;
    }
    private function loadIniFile($file)
    {
        $cache = false;
        $cache_write = '';
        if (strlen($file) < 255 && file_exists($file)) {
            $cache_key = md5(__CLASS__ . '::ini::' . realpath($file));
            if (function_exists('apc_fetch')) {
                $cache = apc_fetch($cache_key);
                $cache_write = 'apc_store';
            } elseif (function_exists('xcache_get') && php_sapi_name() !== 'cli') {
                $cache = xcache_get($cache_key);
                $cache_write = 'xcache_set';
            } elseif (function_exists('eaccelerator_get')) {
                $cache = eaccelerator_get($cache_key);
                $cache_write = 'eaccelerator_put';
            }
            if ($cache && isset($cache['mtime']) && isset($cache['scheme'])
                && filemtime($file) <= $cache['mtime']) {
                $this->_scheme = $this->_bindScheme = unserialize($cache['scheme']);
                $this->_config = $cache['config'];
                return;
            }

            $data = file_get_contents($file);
        } else {
            $data = $file;
            $file = substr(md5($file), 8, 8) . '.ini';
        }
        $this->_config = $this->parseIniData($data);
        if ($this->_config === false) {
            throw new XS_Exception('Failed to parse project config file/string: \'' . substr($file, 0, 10) . '...\'');
        }
        $scheme = new XS_FieldScheme;
        foreach ($this->_config as $key => $value) {
            if (is_array($value)) {
                $scheme->addField($key, $value);
            }
        }
        $scheme->checkValid(true);
        if (!isset($this->_config['project.name'])) {
            $this->_config['project.name'] = basename($file, '.ini');
        }
        $this->_scheme = $this->_bindScheme = $scheme;
        if ($cache_write != '') {
            $cache['mtime'] = filemtime($file);
            $cache['scheme'] = serialize($this->_scheme);
            $cache['config'] = $this->_config;
            call_user_func($cache_write, $cache_key, $cache);
        }
    }
}

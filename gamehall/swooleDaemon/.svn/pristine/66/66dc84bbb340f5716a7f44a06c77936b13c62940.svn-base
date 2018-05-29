<?php

class Swoole_Core_Config {

    private static $config;

    public static function load($configPath) {
        $files = self::tree($configPath, "/.php$/");
        $swooleConfig = array();
        if (! empty($files)) {
            foreach ($files as $file) {
                $swooleConfig += include "{$file}";
            }
        }
        self::$config = $swooleConfig;
        return $swooleConfig;
    }

    public static function loadFiles(array $files) {
        $swooleConfig = array();
        foreach ($files as $file) {
            $swooleConfig += include "{$file}";
        }
        self::$config = $swooleConfig;
        return $swooleConfig;
    }

    public static function get($key, $default = null, $throw = false) {
        $result = isset(self::$config[$key]) ? self::$config[$key] : $default;
        if ($throw && empty($result)) {
            throw new Exception("{$key} config empty");
        }
        return $result;
    }

    public static function getField($key, $filed, $default = null, $throw = false) {
        $result = isset(self::$config[$key][$filed]) ? self::$config[$key][$filed] : $default;
        if ($throw && empty($result)) {
            throw new Exception("{$key} config empty");
        }
        return $result;
    }

    public static function all() {
        return self::$config;
    }
    
    public static function tree($dir, $filter = '', &$result = array()) {
        $files = new DirectoryIterator($dir);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if ($filename[0] === '.') {
                continue;
            }
            if ($file->isDir()) {
                self::tree($dir . DIRECTORY_SEPARATOR . $filename, $filter, $result);
            } else {
                if (! empty($filter) && ! preg_match($filter, $filename)) {
                    continue;
                }
                $result[] = $dir . DIRECTORY_SEPARATOR . $filename;
            }
        }
        return $result;
    }
    
}
